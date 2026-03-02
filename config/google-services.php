<?php

return [
  'backup' => [
    'enabled' => [
      'database' => env('PORTAL_BACKUP_DB_ENABLED', true),
      'storage' => env('PORTAL_BACKUP_STORAGE_ENABLED', true),
      'views' => env('PORTAL_BACKUP_VIEWS_ENABLED', true),
    ],
    'paths' => [
      'database' => 'backups/database',
      'storage' => 'backups/storage',
      'views' => 'backups/views',
    ],
    'sources' => [
      'storage' => env('PORTAL_BACKUP_STORAGE_SOURCE', base_path('storage/app/public')),
      'views' => env('PORTAL_BACKUP_VIEWS_SOURCE', base_path('public/img')),
    ],
    'db' => [
      'dump_binary' => env('PORTAL_BACKUP_DUMP_BINARY', 'mysqldump'),
      'timeout' => env('PORTAL_BACKUP_DB_TIMEOUT', 0),
    ],
  ],
  'drive' => [
    'enabled' => env('GOOGLE_DRIVE_ENABLED', true),
    'scopes' => [
      'https://www.googleapis.com/auth/drive.file',
    ],
    'service_account' => [
      'client_email' => env('GOOGLE_DRIVE_CLIENT_EMAIL'),
      'private_key' => env('GOOGLE_DRIVE_PRIVATE_KEY'),
      'private_key_id' => env('GOOGLE_DRIVE_PRIVATE_KEY_ID'),
      'project_id' => env('GOOGLE_DRIVE_PROJECT_ID'),
      'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
    ],
    'folders' => [
      'database' => env('GOOGLE_DRIVE_DB_FOLDER_ID'),
      'storage' => env('GOOGLE_DRIVE_STORAGE_FOLDER_ID'),
      'views' => env('GOOGLE_DRIVE_VIEWS_FOLDER_ID'),
    ],
    'upload_mime_types' => [
      'sql' => 'application/sql',
      'zip' => 'application/zip',
    ],
    'upload_chunk_size' => env('GOOGLE_DRIVE_UPLOAD_CHUNK_SIZE', 1048576),
    'rate_limit_ms' => env('GOOGLE_DRIVE_RATE_LIMIT_MS', 250),
    'overwrite' => env(true),
    'cleanup_on_failure' => env('PORTAL_BACKUP_CLEANUP_ON_FAILURE', false),
    'log_to_channel' => env('GOOGLE_DRIVE_AUDIT_LOG', false),
    'log_channel' => env('GOOGLE_DRIVE_LOG_CHANNEL', 'stack'),
  ],
];
