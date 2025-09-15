#!/usr/bin/env sh
set -eu

# If we are running the main app (php-fpm), run setup tasks first.
if [ "$#" -eq 0 ] || [ "$1" = "php-fpm" ]; then
  echo "[entrypoint] Running Laravel setup tasks..."

  # Ensure runtime directory exists; populate app into shared volume if empty
  RUNTIME_DIR=/var/www/html
  IMAGE_DIR=/var/www/app
  if [ ! -d "$RUNTIME_DIR" ]; then
    mkdir -p "$RUNTIME_DIR"
  fi
  if [ -z "$(ls -A "$RUNTIME_DIR" 2>/dev/null || true)" ]; then
    echo "[entrypoint] Populating application into runtime volume..."
    cp -a "$IMAGE_DIR"/. "$RUNTIME_DIR"/
  fi
  cd "$RUNTIME_DIR"

  # Ensure storage links are present
  if [ ! -e public/storage ]; then
    php artisan storage:link || true
  fi

  # Run migrations (if DB is reachable). Don't fail container if it can't reach DB yet.
  php artisan migrate --force || echo "[entrypoint] migrate skipped (likely DB not ready)"

  # Cache configuration, routes and views for performance
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true

  # Restart queue workers (no-op if none running)
  php artisan queue:restart || true

  echo "[entrypoint] Launching php-fpm..."
  exec php-fpm
fi

# If a custom command is supplied (e.g., queue:work), execute it directly
exec "$@"
