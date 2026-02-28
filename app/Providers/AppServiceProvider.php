<?php

namespace App\Providers;

use App\Support\Facades\Socialite;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    Paginator::useBootstrap();
    Schema::defaultStringLength(191);
    ini_set('max_execution_time', 120);
    AliasLoader::getInstance()->alias('Socialite', Socialite::class);

    // Email view namespace setup to support custom mail templates in resources/views/vendor/mail
    View::addNamespace('mail', [resource_path('views/vendor/mail/html'), resource_path('views/vendor/mail/text')]);

    // Support enum column migration for Event::event_type
    if (Schema::getConnection()->getDriverName() !== 'sqlite') {
      Schema::getConnection()->getDoctrineConnection()
        ->getDatabasePlatform()
        ->registerDoctrineTypeMapping('enum', 'string');
    }
  }
}