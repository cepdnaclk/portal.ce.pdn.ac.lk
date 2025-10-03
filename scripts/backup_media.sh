#!/usr/bin/env bash
set -euo pipefail

# Directories whose contents should be preserved in the archive.
readonly TARGET_DIRS=(public/img public/storage)

# Users can override the date format via DATE_FORMAT env var (e.g. %Y-%m-%d_%H%M).
readonly DATE_FORMAT="${DATE_FORMAT:-%Y-%m-%d}"

# Determine repository root to keep all paths relative to the project.
if ! git_root=$(git rev-parse --show-toplevel 2>/dev/null); then
  echo "Error: This script must be run from inside a git repository." >&2
  exit 1
fi

cd "$git_root"

# Resolve BACKUP_DIR to an absolute path, defaulting to <repo>/backups.
BACKUP_DIR="${BACKUP_DIR:-$git_root/backups}"
if [[ "$BACKUP_DIR" != /* ]]; then
  BACKUP_DIR="$git_root/$BACKUP_DIR"
fi
mkdir -p "$BACKUP_DIR"

# Only back up directories that currently exist so the script can run on fresh installs.
existing_targets=()
for dir in "${TARGET_DIRS[@]}"; do
  [[ -d "$dir" ]] && existing_targets+=("$dir")
done

if [[ ${#existing_targets[@]} -eq 0 ]]; then
  echo "No target directories found; nothing to back up." >&2
  exit 0
fi

stamp=$(date +"$DATE_FORMAT")
archive_path="$BACKUP_DIR/${stamp}.zip"

# Avoid clobbering an existing backup for the same timestamp.
if [[ -e "$archive_path" ]]; then
  base="${archive_path%.zip}"
  index=1
  while [[ -e "${base}-${index}.zip" ]]; do
    ((index++))
  done
  archive_path="${base}-${index}.zip"
fi

# Use a unique temporary directory and make sure it disappears even on failure.
tmp_dir=$(mktemp -d "$BACKUP_DIR/tmp.XXXXXX")
cleanup() { rm -rf -- "$tmp_dir"; }
trap cleanup EXIT

# Mirror only the contents we care about under their basenames (e.g. img/, storage/).
for dir in "${existing_targets[@]}"; do
  rsync -a "$dir"/ "$tmp_dir/$(basename "$dir")"/
done

( cd "$tmp_dir" && zip -rq "$archive_path" . )

echo "Backup created: $archive_path"
