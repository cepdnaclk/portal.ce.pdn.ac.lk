<?php

namespace App\Support\Facades;

use Laravel\Socialite\Socialite as BaseSocialite;

class Socialite extends BaseSocialite
{
  public static function fake(string $driver = null, $user = null)
  {
    if ($driver === null) {
      $driver = static::defaultDriver();
    }

    return parent::fake($driver, $user);
  }

  private static function defaultDriver(): string
  {
    $services = config('services', []);

    foreach ($services as $key => $config) {
      if (!is_array($config)) {
        continue;
      }

      if (array_key_exists('client_id', $config) || array_key_exists('client_secret', $config)) {
        return (string) $key;
      }
    }

    return 'github';
  }
}
