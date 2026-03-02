<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Http\MediaFileUpload;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class PortalBackup extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'portal:backup';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Export database and assets, then upload to Google Drive';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $overallStart = microtime(true);
    $dateStamp = now()->format('Y-m-d');
    $env = app()->environment();

    $config = config('google-services');
    $backupConfig = Arr::get($config, 'backup', []);
    $driveConfig = Arr::get($config, 'drive', []);

    $artifacts = [];
    $uploaded = [];
    $uploadedDirectories = [];
    $cleanupFiles = [];

    try {
      $this->validateConfig($backupConfig, $driveConfig);

      $enabled = Arr::get($backupConfig, 'enabled', []);
      $paths = Arr::get($backupConfig, 'paths', []);

      $this->ensureDirectory($paths['database'] ?? null);
      $this->ensureDirectory($paths['storage'] ?? null);
      $this->ensureDirectory($paths['views'] ?? null);

      if (!empty($enabled['database'])) {
        $dbFileName = sprintf('portal-%s-%s.sql', $env, $dateStamp);
        $dbPath = rtrim($paths['database'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $dbFileName;
        $this->guardOverwrite($dbPath, $driveConfig);
        $this->runStep('database_export', function () use ($dbPath, $backupConfig) {
          $this->exportDatabase($dbPath, $backupConfig);
        });
        $artifacts[] = $dbPath;
        $cleanupFiles[] = $dbPath;
      }

      if (!empty($enabled['storage'])) {
        $storageFileName = sprintf('portal-storage-%s-%s.zip', $env, $dateStamp);
        $storagePath = rtrim($paths['storage'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storageFileName;
        $this->guardOverwrite($storagePath, $driveConfig);
        $this->runStep('storage_zip', function () use ($storagePath, $backupConfig) {
          $this->zipDirectory(
            Arr::get($backupConfig, 'sources.storage'),
            $storagePath,
            'storage/app/public'
          );
        });
        $artifacts[] = $storagePath;
        $cleanupFiles[] = $storagePath;
      }

      if (!empty($enabled['views'])) {
        $viewsFileName = sprintf('portal-img-%s-%s.zip', $env, $dateStamp);
        $viewsPath = rtrim($paths['views'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $viewsFileName;
        $this->guardOverwrite($viewsPath, $driveConfig);
        $this->runStep('views_zip', function () use ($viewsPath, $backupConfig) {
          $this->zipDirectory(
            Arr::get($backupConfig, 'sources.views'),
            $viewsPath,
            'public/img'
          );
        });
        $artifacts[] = $viewsPath;
        $cleanupFiles[] = $viewsPath;
      }

      if (!empty($driveConfig['enabled'])) {
        $this->runStep('drive_upload', function () use ($artifacts, $driveConfig, $enabled, &$uploaded, &$uploadedDirectories) {
          $client = $this->buildDriveClient($driveConfig);
          $service = new Drive($client);

          foreach ($artifacts as $artifact) {
            $type = $this->resolveArtifactType($artifact);
            if (!$type || empty($enabled[$type])) {
              continue;
            }

            $folderId = Arr::get($driveConfig, "folders.$type");
            $mimeType = $this->resolveMimeType($artifact, $driveConfig);
            $result = $this->uploadToDrive($service, $artifact, $folderId, $mimeType, $driveConfig);
            $uploaded[] = [
              'path' => $artifact,
              'file_id' => $result['id'] ?? null,
              'web_view_link' => $result['webViewLink'] ?? null,
            ];
            $uploadedDirectories[$type] = $this->folderLink($folderId);

            $this->rateLimit($driveConfig);
          }
        });
      }

      $this->auditLog($artifacts, $uploaded, $driveConfig);

      $payload = [
        'status' => 'ok',
        'artifacts' => $artifacts,
        'uploaded_to_drive' => !empty($driveConfig['enabled']),
        'uploaded_directory' => $this->resolvePrimaryFolderLink($driveConfig, $enabled),
        'uploaded_directories' => $uploadedDirectories,
        'uploaded_files' => $uploaded,
      ];

      $this->line(json_encode($payload, JSON_PRETTY_PRINT));
      $this->logStepEvent('backup_complete', 'end', [
        'duration_ms' => $this->durationMs($overallStart),
      ]);

      return self::SUCCESS;
    } catch (Throwable $exception) {
      $this->logStepEvent('backup_failed', 'error', [
        'message' => $exception->getMessage(),
        'duration_ms' => $this->durationMs($overallStart),
      ]);

      $this->error($exception->getMessage());

      if (!empty($driveConfig['cleanup_on_failure'])) {
        $this->cleanupArtifacts($cleanupFiles);
      }

      return self::FAILURE;
    }
  }

  private function validateConfig(array $backupConfig, array $driveConfig): void
  {
    $enabled = Arr::get($backupConfig, 'enabled', []);
    $paths = Arr::get($backupConfig, 'paths', []);

    foreach (['database', 'storage', 'views'] as $key) {
      if (!empty($enabled[$key]) && empty($paths[$key])) {
        throw new \RuntimeException("Missing backup path configuration for {$key}.");
      }
    }

    if (!empty($driveConfig['enabled'])) {
      $serviceAccount = Arr::get($driveConfig, 'service_account', []);
      foreach (['client_email', 'private_key'] as $field) {
        if (empty($serviceAccount[$field])) {
          throw new \RuntimeException("Missing Google service account {$field}.");
        }
      }

      foreach (['database', 'storage', 'views'] as $key) {
        if (!empty($enabled[$key]) && empty($driveConfig['folders'][$key])) {
          throw new \RuntimeException("Missing Google Drive folder ID for {$key}.");
        }
      }
    }
  }

  private function ensureDirectory(?string $path): void
  {
    if (!$path) {
      return;
    }

    if (!is_dir($path)) {
      if (!mkdir($path, 0755, true) && !is_dir($path)) {
        throw new \RuntimeException("Unable to create backup directory: {$path}");
      }
    }

    if (!is_writable($path)) {
      throw new \RuntimeException("Backup directory is not writable: {$path}");
    }
  }

  private function guardOverwrite(string $path, array $driveConfig): void
  {
    if (file_exists($path) && empty($driveConfig['overwrite'])) {
      throw new \RuntimeException("Backup file already exists: {$path}");
    }
  }

  private function exportDatabase(string $outputPath, array $backupConfig): void
  {
    $connection = config('database.default');
    $connectionConfig = config("database.connections.{$connection}");

    if (Arr::get($connectionConfig, 'driver') !== 'mysql') {
      throw new \RuntimeException('Database export currently supports only MySQL connections.');
    }

    $binary = $this->resolveDumpBinary($backupConfig);

    $host = Arr::get($connectionConfig, 'host');
    $port = Arr::get($connectionConfig, 'port', 3306);
    $username = Arr::get($connectionConfig, 'username');
    $password = Arr::get($connectionConfig, 'password');
    $database = Arr::get($connectionConfig, 'database');

    if (!$host || !$username || !$database) {
      throw new \RuntimeException('Database connection configuration is incomplete.');
    }

    $handle = fopen($outputPath, 'wb');
    if (!$handle) {
      throw new \RuntimeException("Unable to write database dump to {$outputPath}");
    }

    $command = [
      $binary,
      '--single-transaction',
      '--quick',
      '--lock-tables=false',
      '-h',
      $host,
      '-P',
      (string) $port,
      '-u',
      $username,
      $database,
    ];

    $process = new Process($command);
    if (!empty($password)) {
      $process->setEnv(['MYSQL_PWD' => $password]);
    }

    $timeout = Arr::get($backupConfig, 'db.timeout', 0);
    if (!empty($timeout)) {
      $process->setTimeout((float) $timeout);
    } else {
      $process->setTimeout(null);
    }

    $process->run(function ($type, $buffer) use ($handle) {
      if ($type === Process::ERR) {
        return;
      }

      fwrite($handle, $buffer);
    });

    fclose($handle);

    if (!$process->isSuccessful()) {
      throw new \RuntimeException('Database export failed: ' . trim($process->getErrorOutput()));
    }
  }

  private function resolveDumpBinary(array $backupConfig): string
  {
    $configured = Arr::get($backupConfig, 'db.dump_binary', 'mysqldump');
    if (is_file($configured) && is_executable($configured)) {
      return $configured;
    }

    $finder = new ExecutableFinder();
    $resolved = $finder->find($configured);

    if (!$resolved) {
      throw new \RuntimeException('mysqldump binary not found. Set PORTAL_BACKUP_DUMP_BINARY to the full path.');
    }

    return $resolved;
  }

  private function zipDirectory(?string $sourcePath, string $outputPath, string $label): void
  {
    if (!$sourcePath || !is_dir($sourcePath)) {
      $this->warn("Source directory missing for {$label}; creating empty archive.");
      $this->createEmptyZip($outputPath);
      return;
    }

    $zip = new ZipArchive();
    if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
      throw new \RuntimeException("Unable to create zip archive: {$outputPath}");
    }

    $sourcePath = rtrim($sourcePath, DIRECTORY_SEPARATOR);
    $files = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($sourcePath, \FilesystemIterator::SKIP_DOTS),
      \RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
      $filePath = $file->getPathname();
      $relativePath = ltrim(str_replace($sourcePath, '', $filePath), DIRECTORY_SEPARATOR);

      if ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
        continue;
      }

      $zip->addFile($filePath, $relativePath);
    }

    $zip->close();
  }

  private function createEmptyZip(string $outputPath): void
  {
    $zip = new ZipArchive();
    if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
      throw new \RuntimeException("Unable to create zip archive: {$outputPath}");
    }

    $zip->close();
  }

  private function buildDriveClient(array $driveConfig): GoogleClient
  {
    $serviceAccount = Arr::get($driveConfig, 'service_account', []);
    $client = new GoogleClient();
    $client->setApplicationName('portal-backup');
    $client->setScopes(Arr::get($driveConfig, 'scopes', [Drive::DRIVE_FILE]));
    $client->setAuthConfig([
      'type' => 'service_account',
      'client_email' => $serviceAccount['client_email'] ?? null,
      'private_key' => $this->normalizePrivateKey($serviceAccount['private_key'] ?? null),
      'private_key_id' => $serviceAccount['private_key_id'] ?? null,
      'project_id' => $serviceAccount['project_id'] ?? null,
      'client_id' => $serviceAccount['client_id'] ?? null,
    ]);

    return $client;
  }

  private function uploadToDrive(Drive $service, string $path, string $folderId, string $mimeType, array $driveConfig): array
  {
    if (!is_readable($path)) {
      throw new \RuntimeException("Backup artifact is not readable: {$path}");
    }

    $fileMetadata = new DriveFile([
      'name' => basename($path),
      'parents' => [$folderId],
    ]);

    $chunkSize = (int) Arr::get($driveConfig, 'upload_chunk_size', 1024 * 1024);

    $client = $service->getClient();
    $client->setDefer(true);

    $request = $service->files->create($fileMetadata, [
      'fields' => 'id, webViewLink',
      'mimeType' => $mimeType,
      'uploadType' => 'multipart',
      'supportsAllDrives' => true
    ]);
    $media = new MediaFileUpload(
      $client,
      $request,
      $mimeType,
      null,
      true,
      $chunkSize
    );

    $media->setFileSize(filesize($path));
    $handle = fopen($path, 'rb');
    if (!$handle) {
      throw new \RuntimeException("Unable to open file for upload: {$path}");
    }

    $status = false;
    while (!$status && !feof($handle)) {
      $chunk = fread($handle, $chunkSize);
      $status = $media->nextChunk($chunk);
    }

    fclose($handle);
    $client->setDefer(false);

    return is_array($status) ? $status : [];
  }

  private function resolveMimeType(string $path, array $driveConfig): string
  {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $types = Arr::get($driveConfig, 'upload_mime_types', []);

    if (!empty($types[$extension])) {
      return $types[$extension];
    }

    return $extension === 'sql' ? 'application/sql' : 'application/zip';
  }

  private function resolveArtifactType(string $path): ?string
  {
    $filename = basename($path);

    if (strpos($filename, 'portal-storage-') === 0) {
      return 'storage';
    }

    if (strpos($filename, 'portal-img-') === 0) {
      return 'views';
    }

    if (strpos($filename, 'portal-') === 0 && substr($filename, -4) === '.sql') {
      return 'database';
    }

    return null;
  }

  private function rateLimit(array $driveConfig): void
  {
    $delayMs = (int) Arr::get($driveConfig, 'rate_limit_ms', 0);
    if ($delayMs > 0) {
      usleep($delayMs * 1000);
    }
  }

  private function logStepEvent(string $step, string $status, array $context = []): void
  {
    $payload = array_merge([
      'step' => $step,
      'status' => $status,
      'timestamp' => now()->toIso8601String(),
    ], $context);

    $this->line(json_encode($payload));
  }

  private function runStep(string $step, callable $callback): void
  {
    $start = microtime(true);
    $this->logStepEvent($step, 'start');

    try {
      $callback();
    } catch (Throwable $exception) {
      $this->logStepEvent($step, 'error', [
        'message' => $exception->getMessage(),
        'duration_ms' => $this->durationMs($start),
      ]);
      throw $exception;
    }

    $this->logStepEvent($step, 'end', [
      'duration_ms' => $this->durationMs($start),
    ]);
  }

  private function durationMs(float $start): int
  {
    return (int) round((microtime(true) - $start) * 1000);
  }

  private function normalizePrivateKey(?string $privateKey): ?string
  {
    if (!$privateKey) {
      return null;
    }

    return str_replace('\\n', "\n", $privateKey);
  }

  private function auditLog(array $artifacts, array $uploaded, array $driveConfig): void
  {
    if (empty($driveConfig['log_to_channel'])) {
      return;
    }

    $channel = $driveConfig['log_channel'] ?? 'stack';
    Log::channel($channel)->info('Portal backup completed', [
      'artifacts' => $artifacts,
      'uploaded' => $uploaded,
    ]);
  }

  private function cleanupArtifacts(array $artifacts): void
  {
    foreach ($artifacts as $artifact) {
      if (is_file($artifact)) {
        unlink($artifact);
      }
    }
  }

  private function folderLink(?string $folderId): ?string
  {
    if (!$folderId) {
      return null;
    }

    return 'https://drive.google.com/drive/folders/' . $folderId;
  }

  private function resolvePrimaryFolderLink(array $driveConfig, array $enabled): ?string
  {
    foreach (['database', 'storage', 'views'] as $type) {
      if (!empty($enabled[$type])) {
        return $this->folderLink($driveConfig['folders'][$type] ?? null);
      }
    }

    return null;
  }
}
