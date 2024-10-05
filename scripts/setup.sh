#! /bin/bash

echo "Running : composer install"
composer install

echo "Running : composer update"
composer update

echo "Running : pnpm install"
pnpm install

echo "Running : pnpm run dev"
pnpm run dev

echo "Running : php artisan storage:link"
php artisan storage:link

echo "Running : php artisan migrate:fresh --seed"
php artisan migrate:fresh --seed
