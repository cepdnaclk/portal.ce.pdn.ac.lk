<?php

namespace App\Domains\Tenant\Models;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\ContentManagement\Models\Event;
use App\Domains\ContentManagement\Models\News;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
  use HasFactory;

  /**
   * @var string[]
   */
  protected $fillable = [
    'slug',
    'name',
    'url',
    'description',
    'is_default',
  ];

  /**
   * @var string[]
   */
  protected $casts = [
    'is_default' => 'boolean',
  ];

  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class, 'tenant_user');
  }

  public function roles(): BelongsToMany
  {
    return $this->belongsToMany(Role::class, 'tenant_role');
  }

  public function news(): HasMany
  {
    return $this->hasMany(News::class);
  }

  public function events(): HasMany
  {
    return $this->hasMany(Event::class);
  }

  public function announcements(): HasMany
  {
    return $this->hasMany(Announcement::class);
  }

  public static function default(): ?self
  {
    $defaultSlug = config('tenants.default');

    return static::query()
      ->where('slug', $defaultSlug)
      ->first() ?? static::query()->where('is_default', true)->first() ?? static::query()->first();
  }

  public static function defaultId(): ?int
  {
    return static::default()?->id;
  }

  public function getNormalizedHost(): ?string
  {
    if (! $this->url) {
      return null;
    }

    $host = parse_url($this->url, PHP_URL_HOST);

    if (! $host) {
      $host = parse_url('https://' . $this->url, PHP_URL_HOST);
    }

    return $host ? Str::lower($host) : null;
  }

  public function __toString(): string
  {
    return (string) $this->name;
  }

  protected static function newFactory()
  {
    return \Database\Factories\TenantFactory::new();
  }
}
