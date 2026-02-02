<?php

namespace Tests\Feature\Api;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function v1_articles_returns_default_tenant_only()
  {
    Article::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);
    $otherTenant = Tenant::factory()->create();

    $defaultArticle = Article::factory()->enabled()->create(['tenant_id' => $defaultTenant->id]);
    Article::factory()->enabled()->create(['tenant_id' => $otherTenant->id]);
    Article::factory()->disabled()->create(['tenant_id' => $defaultTenant->id]);

    $response = $this->getJson('/api/articles/v1');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $defaultArticle->id);
  }

  /** @test */
  public function v2_articles_filters_by_tenant_slug()
  {
    Article::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);
    $otherTenant = Tenant::factory()->create(['slug' => 'other.example.test']);

    $otherArticle = Article::factory()->enabled()->create(['tenant_id' => $otherTenant->id]);
    Article::factory()->enabled()->create(['tenant_id' => $defaultTenant->id]);
    Article::factory()->disabled()->create(['tenant_id' => $otherTenant->id]);

    $response = $this->getJson('/api/articles/v2/' . $otherTenant->slug . '/');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $otherArticle->id);
  }

  /** @test */
  public function v1_articles_can_filter_by_category()
  {
    Article::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);

    $matchingArticle = Article::factory()->enabled()->create([
      'tenant_id' => $defaultTenant->id,
      'categories_json' => ['research', 'awards'],
    ]);
    Article::factory()->enabled()->create([
      'tenant_id' => $defaultTenant->id,
      'categories_json' => ['alumni'],
    ]);
    Article::factory()->disabled()->create([
      'tenant_id' => $defaultTenant->id,
      'categories_json' => ['research'],
    ]);

    $response = $this->getJson('/api/articles/v1/category/research');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $matchingArticle->id);
  }

  /** @test */
  public function v1_article_show_hides_disabled_articles()
  {
    Article::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);

    $disabledArticle = Article::factory()->disabled()->create(['tenant_id' => $defaultTenant->id]);

    $response = $this->getJson('/api/articles/v1/' . $disabledArticle->id);

    $response->assertStatus(404);
    $response->assertJsonFragment(['message' => 'Article not found']);
  }
}