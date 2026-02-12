<?php

namespace Tests\Feature\Backend\Article;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTenantAccessTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function editor_cannot_access_unassigned_tenant_article()
  {
    $user = $this->loginAsEditor();
    $defaultTenant = Tenant::default();
    $otherTenant = Tenant::factory()->create(['slug' => 'restricted.example.test']);

    $user->tenants()->sync([$defaultTenant->id]);

    $article = Article::factory()->create(['tenant_id' => $otherTenant->id]);

    $response = $this->get(route('dashboard.article.edit', $article));

    $response->assertRedirect(route('frontend.index'));
  }

  /** @test */
  public function editor_cannot_create_article_for_unassigned_tenant()
  {
    $this->loginAsEditor();
    $otherTenant = Tenant::factory()->create(['slug' => 'restricted.example.test']);

    $response = $this->post('/dashboard/articles/', [
      'title' => 'Restricted Article',
      'content' => '<p>Not allowed</p>',
      'categories' => 'restricted',
      'tenant_id' => $otherTenant->id,
    ]);

    $response->assertRedirect(route('frontend.index'));
    $this->assertDatabaseMissing('articles', ['title' => 'Restricted Article']);
  }
}