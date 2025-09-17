# Multi-stage Dockerfile for Laravel (PHP-FPM + built assets)

# 1) Composer stage: install PHP dependencies
FROM composer:2 AS composer_deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
  --no-dev \
  --prefer-dist \
  --no-interaction \
  --no-ansi \
  --no-progress \
  --no-scripts

# 2) Node stage: build frontend assets with pnpm + Laravel Mix
FROM node:20-alpine AS node_builder
WORKDIR /app

# Install pnpm matching repo's packageManager version
RUN corepack enable && corepack prepare pnpm@latest --activate
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile

COPY resources ./resources
COPY webpack.mix.js .tsconfig ./

# If your Vite build reads from /public, it will emit to /public/build by default
COPY public ./public

# Provide only what's needed from vendor for Mix copyDirectory
COPY --from=composer_deps /app/vendor ./vendor

# Add build tools
RUN pnpm add -D webpack laravel-mix

RUN pnpm run prod

# 3) Final app image: PHP-FPM runtime
FROM php:8.3-fpm

WORKDIR /var/www/app

# Copy app source
COPY . .

# RUN composer install --no-dev --optimize-autoloader

# Install system dependencies and PHP extensions
RUN set -eux; \
  apt-get update --fix-missing \
  && apt-get install -y --no-install-recommends \
  git bash curl tzdata unzip zip pkg-config \
  libpng-dev libjpeg62-turbo-dev libzip-dev libfreetype6-dev libonig-dev libxml2-dev \
  sqlite3 libsqlite3-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  pdo_mysql pdo_sqlite \
  gd exif bcmath opcache mbstring pcntl \
  && rm -rf /var/lib/apt/lists/*


# Copy Composer vendor from builder
COPY --from=composer_deps /app/vendor ./vendor

# Copy built frontend assets into public (without overwriting index.php)
COPY --from=node_builder /app/public/js ./public/js
COPY --from=node_builder /app/public/css ./public/css
# COPY --from=node_builder /app/mix-manifest.json ./public/mix-manifest.json
# COPY --from=node_builder /app/public/js/tinymce ./public/js/tinymce

# Ensure proper permissions for storage and cache dirs
RUN mkdir -p storage bootstrap/cache \
  && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
  && chown -R www-data:www-data storage bootstrap/cache \
  && find storage -type d -exec chmod 775 {} \; \
  && find storage -type f -exec chmod 664 {} \; \
  && chmod -R 775 bootstrap/cache


## Do not generate APP_KEY at build-time; handled in entrypoint

# Ensure storage & cache are writable
# RUN set -eux; \
#   php artisan key:generate --no-interaction || true \
#   php artisan config:cache; \
#   php artisan route:cache; \
#   php artisan view:cache; \
#   php artisan event:cache || true


# Add entrypoint to run framework optimizations and migrations on boot
COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint

USER www-data

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/app-entrypoint"]
CMD ["php-fpm"]
