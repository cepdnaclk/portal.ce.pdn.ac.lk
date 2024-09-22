#! /bin/bash

echo "Running: Down the site for maintenance"
php artisan down --refresh=30 --render='errors::503'

echo "Running: Update the branch with latest"
git pull

echo "Running: Unit test"
php artisan test

echo "Running: composer install in dev-mode"
composer install

echo "Running: pnpm install"
pnpm install

echo "Running: pnpm run prod"
pnpm run dev

echo "Running: Optimizing the app with caching"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running: Setting permissions"
# sudo chown -R www-data:www-data ./
# sudo find ./ -type f -exec chmod 644 {} \;
# sudo find ./ -type d -exec chmod 755 {} \;

echo "Running: Restarting the queue"
php artisan queue:restart

echo "Running: Disable the maintenance mode"
php artisan up