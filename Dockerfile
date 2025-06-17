# Use official PHP image with Apache
FROM php:8.0-apache

# Install system dependencies
RUN apt-get update \
    && apt-get install -y git unzip zip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Node.js and pnpm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g pnpm \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application source
COPY . .

# Install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --prefer-dist --no-dev --no-interaction

# Build frontend assets
RUN pnpm install --frozen-lockfile \
    && pnpm run prod

# Set proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
