<?php

namespace Tests\Feature\Api;

use App\Domains\Auth\Models\User;
use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function v1_news_includes_author_payload()
  {
    News::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);

    $author = User::factory()->create([
      'name' => 'News Author',
      'email' => 'news.author@example.test',
    ]);

    News::factory()->enabled()->create([
      'tenant_id' => $defaultTenant->id,
      'author_id' => $author->id,
      'created_by' => $author->id,
    ]);

    $response = $this->getJson('/api/news/v1');

    $response->assertOk();
    $response->assertJsonPath('data.0.author.name', 'News Author');
    $response->assertJsonPath('data.0.author.email', 'news.author@example.test');
    $response->assertJsonPath('data.0.author.profile_url', '#');
  }
}