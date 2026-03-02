# Portal Backups (Database + Assets + Google Drive)

## Overview

The `portal:backup` Artisan command exports the MySQL database and zips the asset directories, then uploads the artifacts to Google Drive using a Service Account. Artifacts are stored under `backups/` using environment- and date-specific names.

## Command

```bash
php artisan portal:backup
```

## Restore Command

```bash
php artisan portal:restore 2026-03-02
```

## Artifacts

- Database export: `backups/database/portal-{env}-{yyyy-mm-dd}.sql`
- Public storage: `backups/storage/portal-public-{env}-{yyyy-mm-dd}.zip`
- Images: `backups/views/portal-img-{env}-{yyyy-mm-dd}.zip`

## Prerequisites

- `mysqldump` must be available on the host machine (or configure `PORTAL_BACKUP_DUMP_BINARY` with the full path).
- The Google Drive folders must be shared with the Service Account email.

## Obtaining Google Service Account Credentials

1. Open Google Cloud Console and select the project that will own the backups.
2. Go to `IAM & Admin` → `Service Accounts` and create a new Service Account (or reuse an existing one).
3. Grant the service account the minimal permissions you need (Drive file upload scope is enforced by the API client).
4. Open the Service Account and create a new key of type `JSON`.
5. Download the JSON key file and copy the values into `.env`:
   - `client_email` → `GOOGLE_DRIVE_CLIENT_EMAIL`
   - `private_key` → `GOOGLE_DRIVE_PRIVATE_KEY` (keep line breaks as `\n`)
   - `private_key_id` → `GOOGLE_DRIVE_PRIVATE_KEY_ID`
   - `project_id` → `GOOGLE_DRIVE_PROJECT_ID`
   - `client_id` → `GOOGLE_DRIVE_CLIENT_ID`
6. Share each target Google Drive folder with the Service Account email.

## Environment Variables

```env
# Enable/disable backup sections
PORTAL_BACKUP_DB_ENABLED=true
PORTAL_BACKUP_STORAGE_ENABLED=true
PORTAL_BACKUP_VIEWS_ENABLED=true

# Backup output locations (optional overrides)
PORTAL_BACKUP_DB_PATH=backups/database
PORTAL_BACKUP_STORAGE_PATH=backups/storage
PORTAL_BACKUP_VIEWS_PATH=backups/views

# Source directories
PORTAL_BACKUP_STORAGE_SOURCE=storage/app/public
PORTAL_BACKUP_VIEWS_SOURCE=public/img

# mysqldump binary and timeout
PORTAL_BACKUP_DUMP_BINARY=mysqldump
PORTAL_BACKUP_DB_TIMEOUT=0
PORTAL_BACKUP_RESTORE_BINARY=mysql
PORTAL_BACKUP_DB_RESTORE_TIMEOUT=0

# Cleanup and overwrite behavior
PORTAL_BACKUP_OVERWRITE=false
PORTAL_BACKUP_CLEANUP_ON_FAILURE=false

# Google Drive
GOOGLE_DRIVE_ENABLED=true
GOOGLE_DRIVE_CLIENT_EMAIL=service-account@project.iam.gserviceaccount.com
GOOGLE_DRIVE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n"
GOOGLE_DRIVE_PRIVATE_KEY_ID=...
GOOGLE_DRIVE_PROJECT_ID=...
GOOGLE_DRIVE_CLIENT_ID=...

GOOGLE_DRIVE_DB_FOLDER_ID=...
GOOGLE_DRIVE_STORAGE_FOLDER_ID=...
GOOGLE_DRIVE_VIEWS_FOLDER_ID=...

# Upload tuning
GOOGLE_DRIVE_UPLOAD_CHUNK_SIZE=1048576
GOOGLE_DRIVE_RATE_LIMIT_MS=250
GOOGLE_DRIVE_SQL_MIME=application/sql
GOOGLE_DRIVE_ZIP_MIME=application/zip

# Audit logging (optional)
GOOGLE_DRIVE_AUDIT_LOG=false
GOOGLE_DRIVE_LOG_CHANNEL=stack
```

## Configuration

See `config/google-services.php` for the full set of options. This file controls which backups run, where artifacts are written, Google Drive folder IDs, rate limiting, and upload MIME types.

## Notes

- If source directories are missing, the command creates an empty zip and emits a warning.
- Large databases may require higher PHP timeouts; consider running with `PORTAL_BACKUP_DB_TIMEOUT=0` (no timeout) and ensure adequate disk space.
- The command emits structured JSON logs per step; the final output includes the list of artifacts and uploaded file IDs.
