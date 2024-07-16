REM Update PHP dependencies
composer update

REM Update NodeJS dependencies and compile
npm install
npm run dev

REM Migrate the DB into latest status
php artisan migrate