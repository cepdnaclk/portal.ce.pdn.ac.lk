<?php

namespace App\Domains\ContentManagement\Models;

use Database\Factories\ArticleFactory;

/**
 * Class Article.
 */
class Article extends BaseContent
{

  /**
   * @var string[]
   */
  protected $attributes = [
    'categories_json' => '[]',
    'gallery_json' => '[]',
    'content_images_json' => '[]',
  ];

  /**
   * @var string[]
   */
  protected $fillable = [
    'title',
    'content',
    'published_at',
    'categories_json',
    'gallery_json',
    'content_images_json',
    'tenant_id',
    'enabled',
  ];

  /**
   * @var string[]
   */
  protected $casts = [
    'published_at' => 'datetime',
    'categories_json' => 'array',
    'gallery_json' => 'array',
    'content_images_json' => 'array',
    'enabled' => 'boolean',
  ];

  /**
   * Create a new factory instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  protected static function newFactory()
  {
    return ArticleFactory::new();
  }

  /**
   * @param $query
   * @param $tenant
   * @return mixed
   */
  public function scopeForTenant($query, $tenant)
  {
    $tenantId = $tenant instanceof \App\Domains\Tenant\Models\Tenant ? $tenant->id : $tenant;

    return $tenantId ? $query->where('tenant_id', $tenantId) : $query;
  }

  /**
   * @param $query
   * @param array $tenantIds
   * @return mixed
   */
  public function scopeForTenants($query, array $tenantIds)
  {
    return $query->whereIn('tenant_id', $tenantIds);
  }

  /**
   * @param $query
   * @param string $category
   * @return mixed
   */
  public function scopeWithCategory($query, string $category)
  {
    if ($query->getConnection()->getDriverName() === 'sqlite') {
      return $query->where('categories_json', 'like', '%"' . $category . '"%');
    }

    return $query->whereJsonContains('categories_json', $category);
  }

  /**
   * @param $query
   * @return mixed
   */
  public function scopeEnabled($query)
  {
    return $query->whereEnabled(true);
  }
}