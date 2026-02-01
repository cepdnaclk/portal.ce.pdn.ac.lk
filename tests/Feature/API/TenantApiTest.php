<?php

namespace Tests\Feature\API;

use App\Domains\ContentManagement\Models\Event;
use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantApiTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function v1_news_returns_default_tenant_only()
  {
    News::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);
    $otherTenant = Tenant::factory()->create();

    $defaultNews = News::factory()->create(['tenant_id' => $defaultTenant->id, 'enabled' => 1]);
    News::factory()->create(['tenant_id' => $otherTenant->id, 'enabled' => 1]);

    $response = $this->getJson('/api/news/v1');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $defaultNews->id);
  }

  /** @test */
  public function v1_events_return_default_tenant_only()
  {
    Event::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);
    $otherTenant = Tenant::factory()->create();

    $defaultEvent = Event::factory()->create(['tenant_id' => $defaultTenant->id, 'enabled' => 1]);
    Event::factory()->create(['tenant_id' => $otherTenant->id, 'enabled' => 1]);

    $response = $this->getJson('/api/events/v1');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $defaultEvent->id);
  }

  /** @test */
  public function v2_news_filters_by_tenant_slug()
  {
    News::query()->delete();

    $defaultTenant = Tenant::default() ?? Tenant::factory()->create([
      'slug' => config('tenants.default'),
      'url' => 'https://' . config('tenants.default'),
      'is_default' => true,
    ]);
    $otherTenant = Tenant::factory()->create(['slug' => 'other.example.test']);

    $otherNews = News::factory()->create(['tenant_id' => $otherTenant->id, 'enabled' => 1]);
    News::factory()->create(['tenant_id' => $defaultTenant->id, 'enabled' => 1]);

    $response = $this->getJson('/api/news/v2/' . $otherTenant->slug . '/');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $otherNews->id);
  }

  /** @test */
  public function v2_returns_not_found_for_unknown_tenant_slug()
  {
    $response = $this->getJson('/api/news/v2/unknown-tenant/');

    $response->assertStatus(404);
    $response->assertJsonFragment(['message' => 'Tenant not found']);
  }
}
