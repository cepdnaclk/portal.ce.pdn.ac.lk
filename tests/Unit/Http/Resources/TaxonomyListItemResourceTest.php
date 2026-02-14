<?php

namespace Tests\Unit\Http\Resources;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Taxonomy\Models\TaxonomyList;
use App\Domains\Tenant\Models\Tenant;
use App\Http\Resources\TaxonomyListItemResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyListItemResourceTest extends TestCase
{
  use RefreshDatabase;

  public function test_article_items_are_mapped_with_urls_and_missing_flags()
  {
    $tenant = Tenant::factory()->create(['slug' => 'unit-tenant.test']);
    $article = Article::factory()->create(['tenant_id' => $tenant->id, 'title' => 'Unit Article']);
    $missingId = $article->id + 999;

    $taxonomyList = TaxonomyList::factory()->create([
      'name' => 'Articles',
      'data_type' => 'article',
      'items' => [$article->id, $missingId],
    ]);

    $payload = (new TaxonomyListItemResource($taxonomyList))->toArray(request());

    $this->assertSame('article', $payload['data_type']);
    $this->assertCount(2, $payload['items']);

    $firstItem = $payload['items'][0];
    $this->assertSame($article->id, $firstItem['id']);
    $this->assertSame($article->title, $firstItem['slug']);
    $this->assertSame(
      route('api.v2.articles.show', ['id' => $article->id, 'tenant_slug' => $tenant->slug]),
      $firstItem['url']
    );

    $secondItem = $payload['items'][1];
    $this->assertSame($missingId, $secondItem['id']);
    $this->assertTrue($secondItem['missing']);
  }
}
