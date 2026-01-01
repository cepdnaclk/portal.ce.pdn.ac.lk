<?php

namespace App\Domains\Auth\Models;

use App\Domains\Auth\Models\Traits\Attribute\RoleAttribute;
use App\Domains\Auth\Models\Traits\Method\RoleMethod;
use App\Domains\Auth\Models\Traits\Scope\RoleScope;
use App\Domains\Tenant\Models\Tenant;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Class Role.
 */
class Role extends SpatieRole
{
  use HasFactory,
    RoleAttribute,
    RoleMethod,
    RoleScope;

  /**
   * @var string[]
   */
  protected $with = [
    'permissions',
  ];

  /**
   * Create a new factory instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  protected static function newFactory()
  {
    return RoleFactory::new();
  }

  public function tenants(): BelongsToMany
  {
    return $this->belongsToMany(Tenant::class, 'tenant_role');
  }
}