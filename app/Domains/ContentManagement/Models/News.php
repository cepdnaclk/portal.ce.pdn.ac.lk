<?php

namespace App\Domains\ContentManagement\Models;

use App\Domains\ContentManagement\Models\BaseContent;
use Database\Factories\NewsFactory;
use App\Domains\ContentManagement\Models\Traits\Scope\NewsScope;

/**
 * Class News.
 */
class News extends BaseContent
{
  use NewsScope;

  /**
   * @var string[]
   */
  protected $fillable = [
    'title',
    'url',
    'description',
    'image',
    'link_url',
    'link_caption',
    'published_at',
    'tenant_id',
  ];

  /**
   * @var string[]
   */
  protected $casts = [
    'enabled' => 'boolean',
  ];

  /**
   * Create a new factory instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  protected static function newFactory()
  {
    return NewsFactory::new();
  }
}