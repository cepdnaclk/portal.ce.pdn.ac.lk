[![Laravel CI](https://github.com/cepdnaclk/portal.ce.pdn.ac.lk/actions/workflows/laravel_push.yml/badge.svg)](https://github.com/cepdnaclk/portal.ce.pdn.ac.lk/actions/workflows/laravel_push.yml) ![GitHub Release](https://img.shields.io/github/v/release/cepdnaclk/portal.ce.pdn.ac.lk) [![codecov](https://codecov.io/gh/cepdnaclk/portal.ce.pdn.ac.lk/graph/badge.svg?token=T039QAP0FR)](https://codecov.io/gh/cepdnaclk/portal.ce.pdn.ac.lk)

# portal.ce.pdn.ac.lk

Internal and Public web service provider of the Department of Computer Engineering

## Team of Developers

- [https://portal.ce.pdn.ac.lk/contributors](https://portal.ce.pdn.ac.lk/contributors)

## Useful Commands and Instructions

You need to install WAMP or XAMP server and run it before following commands.
Please make sure you already created a Database and a Database User Account.

#### Install Dependencies

```bash
// Install PHP dependencies
composer install

// Install Node dependencies (development mode, can use `npm` as well, but recommended to use `pnpm` here)
pnpm install
pnpm run dev
```

##### Additional useful commands

```bash
// If you received mmap() error, use this command
php -d memory_limit=-1 /usr/local/bin/composer install

// Update PHP dependencies
composer update

// Prepare the database
php artisan migrate
```

#### Prepare for the first run

First you need to copy `.env.example` and save as `.env` in the root folder, and change the `Admin` and `User` credentials, `Database` configurations.

Next follow the below commands

```bash
// Prepare the public link for storage
php artisan storage:link

// Reset the database and seed the data
php artisan migrate:fresh --seed

// Prepare webhook for unit testing
git config --local core.hooksPath .githooks

```

#### Serve in the Local environment

```bash
// Serve PHP web server
php artisan serve

// Serve PHP web server, in a specific IP & port
php artisan serve --host=0.0.0.0 --port=8000

// To work with Vue components, you need to run this in parallel (can use `npm` as well, but recommended to use `pnpm` here)
pnpm run watch
```

#### Cache and optimization

```bash
// Remove dev dependencies
composer install --optimize-autoloader --no-dev

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan optimize:clear

php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Maintenance related commands

```bash
php artisan down --message="{Message}" --retry=60
php artisan up
```

#### Other useful instructions

```bash
// Create Model, Controller and Database Seeder
php artisan make:model {name} --migration --controller --seed

// Create a Email
php artisan make:mail -m

// Commandline interface for Database Operations
php artisan tinker

// Run the unit tests
php artisan test

// Populate the DB with a specific Seeder class
php artisan db:seed --class=<path-to-the-seeder-class>

// Ex: php artisan db:seed --class=Database\\Seeders\\Roles\\AcademicRoleSeeder
```

#### Maintenance Scripts

Can be found under `./scripts.` folder. In the production environment, scripts need to be run with `sudo` from the base directory to work correctly.

Ex:

```bash
sudo sh ./scripts/deploy-prod.sh
```

#### Resource Routes - Standardard Pattern

| Verb   | URI                    | Action  | Route Name     |
| :----- | :--------------------- | :------ | :------------- |
| GET    | /photos/               | index   | photos.index   |
| GET    | /photos/create         | create  | photos.create  |
| GET    | /photos/view/{photo}   | show    | photos.show    |
| GET    | /photos/edit/{photo}   | edit    | photos.edit    |
| GET    | /photos/delete/{photo} | delete  | photos.delete  |
| POST   | /photos/               | store   | photos.store   |
| PUT    | /photos/{photo}        | update  | photos.update  |
| DELETE | /photos/{photo}        | destroy | photos.destroy |

## Contributors

Thanks to all the contributors who have helped with this project!

<a href="https://portal.ce.pdn.ac.lk/contributors">
  https://portal.ce.pdn.ac.lk/contributors
  <br/><br/>
  <img src="https://contrib.rocks/image?repo=cepdnaclk/portal.ce.pdn.ac.lk" />
</a>
