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

  # Ensure framework cache paths exist (required by artisan)
  mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache || true

  # Generate app key if missing (avoid baking secrets at build time)
  # Consider key missing if APP_KEY env is empty or .env has an empty APP_KEY
  if [ -z "${APP_KEY:-}" ]; then
    # Also check .env content if exists
    if [ ! -f .env ] || ! grep -q '^APP_KEY=.' .env; then
      echo "[entrypoint] Generating APP_KEY..."
      php artisan key:generate --force --no-interaction || echo "[entrypoint] key:generate failed (will continue)"
    fi
  fi

  # Ensure storage links are present
  if [ ! -e public/storage ]; then
    php artisan storage:link || true
  fi

  # Run migrations (if DB is reachable). Don't fail container if it can't reach DB yet.
  php artisan migrate --force || echo "[entrypoint] migrate skipped (likely DB not ready)"

  # Cache configuration, routes and views for performance (after key generation)
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
