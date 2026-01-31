<?php

namespace App\Providers;

use App\Support\Facades\Socialite;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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

    // Support enum column migration for Event::event_type
    if (Schema::getConnection()->getDriverName() !== 'sqlite') {
      Schema::getConnection()->getDoctrineConnection()
        ->getDatabasePlatform()
        ->registerDoctrineTypeMapping('enum', 'string');
    }
  }
}
