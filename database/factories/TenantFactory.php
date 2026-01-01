<?php

namespace Database\Factories;

use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
  protected $model = Tenant::class;

  public function definition()
  {
    $host = $this->faker->domainName;

    return [
      'slug' => $host,
      'name' => Str::title(str_replace('.', ' ', $host)),
      'url' => 'https://' . $host,
      'description' => $this->faker->words(3, true),
      'is_default' => false,
    ];
  }
}