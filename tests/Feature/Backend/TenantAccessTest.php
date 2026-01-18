<?php

namespace Tests\Feature\Backend;

use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantAccessTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function editor_cannot_access_unassigned_tenant_content()
  {
    $user = $this->loginAsEditor();
    $defaultTenant = Tenant::default();
    $otherTenant = Tenant::factory()->create(['slug' => 'restricted.example.test']);

    $user->tenants()->sync([$defaultTenant->id]);

    $news = News::factory()->create(['tenant_id' => $otherTenant->id]);

    $response = $this->get(route('dashboard.news.edit', $news));

    $response->assertRedirect(route('frontend.index'));
  }

  /** @test */
  public function editor_cannot_create_content_for_unassigned_tenant()
  {
    $this->loginAsEditor();
    $otherTenant = Tenant::factory()->create(['slug' => 'restricted.example.test']);

    $response = $this->post('/dashboard/news/', [
      'title' => 'Restricted News',
      'description' => 'Not allowed',
      'url' => 'restricted-news',
      'published_at' => '2024-12-12',
      'tenant_id' => $otherTenant->id,
    ]);

    $response->assertRedirect(route('frontend.index'));
    $this->assertDatabaseMissing('news', ['title' => 'Restricted News']);
  }
}