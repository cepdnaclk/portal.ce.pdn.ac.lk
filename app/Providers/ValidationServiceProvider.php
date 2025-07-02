<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {

        /*
        |--------------------------------------------------------------------------
        | slug: lowercase letters + digits, separated by single hyphens
        |      no leading/trailing hyphen, no consecutive hyphens
        |--------------------------------------------------------------------------
        | Examples of valid slugs:
        |   post-1                 ✅
        |   hello-world-2025       ✅
        |   foo-123-bar            ✅
        |
        | Examples of invalid slugs:
        |   Hello-World            ❌ (uppercase)
        |   leading--dash          ❌ (double hyphen)
        |   -starts-with-dash      ❌
        |   ends-with-dash-        ❌
        */
        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value);
        });
        Validator::replacer('slug', function ($message, $attribute) {
            return "The {$attribute} may only contain lowercase letters, numbers and single hyphens, and cannot begin or end with a hyphen.";
        });
    }
}
