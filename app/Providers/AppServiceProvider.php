<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use App\Support\Html\FormBuilder;
use Illuminate\Http\RedirectResponse;
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
        $this->app->singleton('form', function () {
            return new FormBuilder();
        });
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

        // Support enum column migration for Event::event_type
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::getConnection()->getDoctrineConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');
        }

        RedirectResponse::macro('withFlashSuccess', function ($message) {
            return $this->with('flash_success', $message);
        });

        RedirectResponse::macro('withFlashDanger', function ($message) {
            return $this->with('flash_danger', $message);
        });

        RedirectResponse::macro('withFlashWarning', function ($message) {
            return $this->with('flash_warning', $message);
        });

        RedirectResponse::macro('withFlashInfo', function ($message) {
            return $this->with('flash_info', $message);
        });
    }
}
