<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class PortalRestore extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'portal:restore {date : Backup date (YYYY-MM-DD)}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Restore database and assets from Google Drive backups';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $overallStart = microtime(true);
    $date = (string) $this->argument('date');
    $env = app()->environment();

    $this->validateDate($date);

    $config = config('google-services');
    $backupConfig = Arr::get($config, 'backup', []);
    $driveConfig = Arr::get($config, 'drive', []);

    $artifacts = [];
    $restored = [];
    $cleanupFiles = [];

    try {
      $this->validateConfig($backupConfig, $driveConfig);

      if (empty($driveConfig['enabled'])) {
        throw new \RuntimeException('Google Drive integration is disabled.');
      }

      $enabled = Arr::get($backupConfig, 'enabled', []);
      $paths = Arr::get($backupConfig, 'paths', []);
      $sources = Arr::get($backupConfig, 'sources', []);

      $this->ensureDirectory($paths['database'] ?? null);
      $this->ensureDirectory($paths['storage'] ?? null);
      $this->ensureDirectory($paths['views'] ?? null);

      $client = $this->buildDriveClient($driveConfig);
      $service = new Drive($client);

      if (!empty($enabled['database'])) {
        $dbFileName = sprintf('portal-%s-%s.sql', $env, $date);
        $dbPath = rtrim($paths['database'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $dbFileName;
        $this->runStep('database_download', function () use ($service, $driveConfig, $dbFileName, $dbPath) {
          $this->downloadFromDrive($service, $driveConfig, 'database', $dbFileName, $dbPath);
        });
        $artifacts[] = $dbPath;
        $cleanupFiles[] = $dbPath;

        $this->runStep('database_restore', function () use ($dbPath, $backupConfig) {
          $this->importDatabase($dbPath, $backupConfig);
        });
        $restored[] = $dbPath;
      }

      if (!empty($enabled['storage'])) {
        $storageFileName = sprintf('portal-storage-%s-%s.zip', $env, $date);
        $storagePath = rtrim($paths['storage'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storageFileName;
        $this->runStep('storage_download', function () use ($service, $driveConfig, $storageFileName, $storagePath) {
          $this->downloadFromDrive($service, $driveConfig, 'storage', $storageFileName, $storagePath);
        });
        $artifacts[] = $storagePath;
        $cleanupFiles[] = $storagePath;

        $this->runStep('storage_restore', function () use ($storagePath, $sources) {
          $this->extractZip($storagePath, Arr::get($sources, 'storage'), 'storage/app/public');
        });
        $restored[] = $storagePath;
      }

      if (!empty($enabled['views'])) {
        $viewsFileName = sprintf('portal-img-%s-%s.zip', $env, $date);
        $viewsPath = rtrim($paths['views'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $viewsFileName;
        $this->runStep('views_download', function () use ($service, $driveConfig, $viewsFileName, $viewsPath) {
          $this->downloadFromDrive($service, $driveConfig, 'views', $viewsFileName, $viewsPath);
        });
        $artifacts[] = $viewsPath;
        $cleanupFiles[] = $viewsPath;

        $this->runStep('views_restore', function () use ($viewsPath, $sources) {
          $this->extractZip($viewsPath, Arr::get($sources, 'views'), 'public/img');
        });
        $restored[] = $viewsPath;
      }

      $this->auditLog($restored, $driveConfig);

      $payload = [
        'status' => 'ok',
        'date' => $date,
        'artifacts' => $artifacts,
        'restored' => $restored,
      ];

      $this->line(json_encode($payload, JSON_PRETTY_PRINT));
      $this->logStepEvent('restore_complete', 'end', [
        'duration_ms' => $this->durationMs($overallStart),
      ]);

      return self::SUCCESS;
    } catch (Throwable $exception) {
      $this->logStepEvent('restore_failed', 'error', [
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

  private function validateDate(string $date): void
  {
    $valid = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    if (!$valid) {
      throw new \RuntimeException('Date must be in YYYY-MM-DD format.');
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

  private function ensureDirectory(?string $path): void
  {
    if (!$path) {
      return;
    }

    if (!is_dir($path)) {
      if (!mkdir($path, 0755, true) && !is_dir($path)) {
        throw new \RuntimeException("Unable to create directory: {$path}");
      }
    }

    if (!is_writable($path)) {
      throw new \RuntimeException("Directory is not writable: {$path}");
    }
  }

  private function buildDriveClient(array $driveConfig): GoogleClient
  {
    $serviceAccount = Arr::get($driveConfig, 'service_account', []);
    $client = new GoogleClient();
    $client->setApplicationName('portal-restore');
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

  private function downloadFromDrive(Drive $service, array $driveConfig, string $type, string $fileName, string $destination): void
  {
    $folderId = Arr::get($driveConfig, "folders.{$type}");
    $query = sprintf("name = '%s' and '%s' in parents and trashed = false", addslashes($fileName), $folderId);

    $listParams = [
      'q' => $query,
      'fields' => 'files(id, name, modifiedTime)',
      'pageSize' => 1,
      'orderBy' => 'modifiedTime desc',
      'supportsAllDrives' => true,
      'includeItemsFromAllDrives' => true,
      'corpora' => Arr::get($driveConfig, 'corpora', 'allDrives'),
    ];

    $files = $service->files->listFiles($listParams)->getFiles();
    if (empty($files)) {
      throw new \RuntimeException("Backup file not found in Drive folder: {$fileName}");
    }

    $fileId = $files[0]->getId();
    $response = $service->files->get($fileId, [
      'alt' => 'media',
      'supportsAllDrives' => true,
    ]);

    $body = $response->getBody();
    $handle = fopen($destination, 'wb');
    if (!$handle) {
      throw new \RuntimeException("Unable to write file: {$destination}");
    }

    while (!$body->eof()) {
      fwrite($handle, $body->read(1024 * 1024));
    }

    fclose($handle);
  }

  private function importDatabase(string $inputPath, array $backupConfig): void
  {
    $connection = config('database.default');
    $connectionConfig = config("database.connections.{$connection}");

    if (Arr::get($connectionConfig, 'driver') !== 'mysql') {
      throw new \RuntimeException('Database restore currently supports only MySQL connections.');
    }

    if (!is_readable($inputPath)) {
      throw new \RuntimeException("Database backup file not readable: {$inputPath}");
    }

    $binary = $this->resolveMysqlBinary($backupConfig);

    $host = Arr::get($connectionConfig, 'host');
    $port = Arr::get($connectionConfig, 'port', 3306);
    $username = Arr::get($connectionConfig, 'username');
    $password = Arr::get($connectionConfig, 'password');
    $database = Arr::get($connectionConfig, 'database');

    if (!$host || !$username || !$database) {
      throw new \RuntimeException('Database connection configuration is incomplete.');
    }

    $handle = fopen($inputPath, 'rb');
    if (!$handle) {
      throw new \RuntimeException("Unable to open database backup: {$inputPath}");
    }

    $command = [
      $binary,
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

    $timeout = Arr::get($backupConfig, 'db.restore_timeout', 0);
    if (!empty($timeout)) {
      $process->setTimeout((float) $timeout);
    } else {
      $process->setTimeout(null);
    }

    $process->setInput($handle);
    $process->run();

    fclose($handle);

    if (!$process->isSuccessful()) {
      throw new \RuntimeException('Database restore failed: ' . trim($process->getErrorOutput()));
    }
  }

  private function resolveMysqlBinary(array $backupConfig): string
  {
    $configured = Arr::get($backupConfig, 'db.restore_binary', 'mysql');
    if (is_file($configured) && is_executable($configured)) {
      return $configured;
    }

    $finder = new ExecutableFinder();
    $resolved = $finder->find($configured);

    if (!$resolved) {
      throw new \RuntimeException('mysql binary not found. Set PORTAL_BACKUP_RESTORE_BINARY to the full path.');
    }

    return $resolved;
  }

  private function extractZip(string $zipPath, ?string $destination, string $label): void
  {
    if (!$destination) {
      throw new \RuntimeException("Missing restore destination for {$label}.");
    }

    if (!is_readable($zipPath)) {
      throw new \RuntimeException("Backup archive not readable: {$zipPath}");
    }

    if (!is_dir($destination)) {
      if (!mkdir($destination, 0755, true) && !is_dir($destination)) {
        throw new \RuntimeException("Unable to create restore directory: {$destination}");
      }
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
      throw new \RuntimeException("Unable to open zip archive: {$zipPath}");
    }

    $zip->extractTo($destination);
    $zip->close();
  }

  private function auditLog(array $restored, array $driveConfig): void
  {
    if (empty($driveConfig['log_to_channel'])) {
      return;
    }

    $channel = $driveConfig['log_channel'] ?? 'stack';
    Log::channel($channel)->info('Portal restore completed', [
      'restored' => $restored,
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
}
