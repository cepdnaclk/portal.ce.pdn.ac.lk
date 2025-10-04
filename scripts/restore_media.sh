#!/usr/bin/env bash
set -euo pipefail

# Directories expected inside the backup archive and their restore targets.
readonly TARGET_DIRS=(public/img public/storage)

# Ensure we operate from the repository root so paths line up.
if ! git_root=$(git rev-parse --show-toplevel 2>/dev/null); then
  echo "Error: This script must be run from inside a git repository." >&2
  exit 1
fi

cd "$git_root"

# Resolve BACKUP_DIR so users can override it (relative or absolute).
BACKUP_DIR="${BACKUP_DIR:-$git_root/backups}"
if [[ "$BACKUP_DIR" != /* ]]; then
  BACKUP_DIR="$git_root/$BACKUP_DIR"
fi

if [[ ! -d "$BACKUP_DIR" ]]; then
  echo "Error: Backup directory '$BACKUP_DIR' does not exist." >&2
  exit 1
fi

# Gather available backup archives.
shopt -s nullglob
backup_files=("$BACKUP_DIR"/*.zip)
shopt -u nullglob
IFS=$'\n' backup_files=($(printf '%s\n' "${backup_files[@]}" | sort))
unset IFS

if [[ ${#backup_files[@]} -eq 0 ]]; then
  echo "No backup archives found in $BACKUP_DIR." >&2
  exit 1
fi

# If the user provided an explicit archive path, prefer that instead of prompting.
if [[ $# -gt 0 ]]; then
  candidate=$1
  if [[ "$candidate" != /* ]]; then
    candidate="$BACKUP_DIR/$candidate"
  fi
  if [[ ! -f "$candidate" ]]; then
    echo "Error: Archive '$candidate' not found." >&2
    exit 1
  fi
  selected_archive="$candidate"
else
  echo "Available backup archives:"
  choices=()
  for archive in "${backup_files[@]}"; do
    choices+=("$(basename "$archive")")
  done
  PS3="Select a backup to restore (or 0 to cancel): "
  select choice in "${choices[@]}"; do
    if [[ -z "${choice:-}" ]]; then
      echo "Restore cancelled." >&2
      exit 1
    fi
    selected_archive="$BACKUP_DIR/$choice"
    break
  done
fi

echo "Restoring from '$selected_archive'"

# Use a dedicated temp directory and ensure it vanishes even if the script exits early.
tmp_dir=$(mktemp -d "$BACKUP_DIR/restore.XXXXXX")
cleanup() { rm -rf -- "$tmp_dir"; }
trap cleanup EXIT

unzip -q "$selected_archive" -d "$tmp_dir"

# Copy each expected directory back to its target location.
for target in "${TARGET_DIRS[@]}"; do
  base=$(basename "$target")
  src="$tmp_dir/$base"
  if [[ ! -d "$src" ]]; then
    echo "Warning: Archive missing '$base'; skipped restoring '$target'." >&2
    continue
  fi
  mkdir -p "$target"
  rsync -a "$src"/ "$target"/
done

echo "Restore complete."
