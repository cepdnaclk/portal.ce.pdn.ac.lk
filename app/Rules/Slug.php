<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Slug implements Rule
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

  public function __construct()
  {
    //
  }

  public function passes($attribute, $value): bool
  {
    return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value);
  }

  public function message(): string
  {
    return "The :attribute may only contain lowercase letters, numbers and single hyphens, and cannot begin or end with a hyphen.";
  }
}