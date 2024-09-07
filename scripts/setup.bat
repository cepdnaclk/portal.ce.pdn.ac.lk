@echo off
cd ..
copy .env.example .env
@echo on

echo "Running : composer install"
composer install

echo "Running : composer update"
composer update

echo "Running : npm install"
npm install

echo "Running : npm run dev"
npm run dev

echo "Running : php artisan storage:link"
php artisan storage:link

echo "Running : php artisan migrate"
php artisan migrate

echo "Running : php artisan migrate:fresh --seed"
php artisan migrate:fresh --seed

php artisan storage:link

php artisan key:generate