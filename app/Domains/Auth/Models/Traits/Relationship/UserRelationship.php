<?php

namespace App\Domains\Auth\Models\Traits\Relationship;

use App\Domains\Auth\Models\PasswordHistory;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
  /**
   * @return mixed
   */
  public function passwordHistories()
  {
    return $this->morphMany(PasswordHistory::class, 'model');
  }

  public function tenants(): BelongsToMany
  {
    return $this->belongsToMany(Tenant::class, 'tenant_user');
  }
}