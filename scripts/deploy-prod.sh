#! /bin/bash

echo "Running: Down the site for maintenance"
php artisan down --refresh=30 --render='errors::503'

echo "Running: Update the branch with latest"
sudo git pull

echo "Running: composer install in prod-mode"
composer install --optimize-autoloader --no-dev
composer dump-autoload

echo "Running: pnpm install"
pnpm install

echo "Running: pnpm run prod"
pnpm run prod

echo "Running: migrate the database (no seed)"
php artisan migrate

# Not running. Should check on CI/CD level
# echo "Running: Unit test"
# touch database/database.sqlite
# php artisan test

echo "Running: Optimizing the app with caching"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running: Setting permissions"
sudo chown -R www-data:www-data ./

# Set directory permissions to 755 (rwxr-xr-x)
sudo find ./ -type d -exec chmod 755 {} \;

# Set file permissions to 644 (rw-r--r--)
sudo find ./ -type f -exec chmod 644 {} \;

# Ensure storage and cache directories are writable
sudo chmod -R 775 ./storage
sudo chmod -R 775 ./bootstrap/cache


echo "Running: Restarting the queue"
php artisan queue:restart

echo "Running: Disable the maintenance mode"
php artisan up