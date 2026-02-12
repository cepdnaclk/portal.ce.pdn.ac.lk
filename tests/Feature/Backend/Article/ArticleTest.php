<?php

namespace Tests\Feature\Backend\Article;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use  App\Domains\Auth\Models\User;

class ArticleTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function an_article_editor_can_access_the_list_article_page()
  {
    $this->loginAsEditor();
    $this->get('/dashboard/articles/')->assertOk();
  }

  /** @test */
  public function an_article_editor_can_access_the_create_article_page()
  {
    $this->loginAsEditor();
    $this->get('/dashboard/articles/create')->assertOk();
  }

  /** @test */
  public function an_article_editor_can_access_the_delete_article_page()
  {
    $this->loginAsEditor();
    $article = Article::factory()->create();
    $this->get('/dashboard/articles/delete/' . $article->id)->assertOk();
  }

  /** @test */
  public function article_can_be_created()
  {
    $editor = $this->loginAsEditor();
    $otherAuthor = User::factory()->create();
    $tenantId = Tenant::defaultId();

    $response = $this->post('/dashboard/articles/', [
      'title' => 'test Article',
      'content' => '<p>Article content</p>',
      'categories' => 'research, alumni',
      'content_images_json' => json_encode([]),
      'enabled' => 1,
      'tenant_id' => $tenantId,
      'author_id' => $otherAuthor->id,
    ]);

    $response->assertStatus(302);
    $article = Article::where('title', 'test Article')->first();
    $this->assertNotNull($article);
    $this->assertSame($editor->id, $article->author_id);
  }

  /** @test */
  public function article_can_be_updated()
  {
    $this->loginAsEditor();
    $article = Article::factory()->create();
    $tenantId = Tenant::defaultId();
    $author = \App\Domains\Auth\Models\User::factory()->create();

    $updateData = [
      'title' => 'Updated Article',
      'content' => '<p>Updated article content</p>',
      'categories' => 'awards',
      'content_images_json' => json_encode([]),
      'enabled' => 0,
      'tenant_id' => $tenantId,
      'author_id' => $author->id,
    ];

    $response = $this->put("/dashboard/articles/{$article->id}", $updateData);
    $response->assertStatus(302);

    $this->assertDatabaseHas('articles', [
      'title' => 'Updated Article',
      'author_id' => $author->id,
    ]);
  }

  /** @test */
  public function article_can_be_deleted()
  {
    $this->loginAsEditor();
    $article = Article::factory()->create();
    $this->delete('/dashboard/articles/' . $article->id);
    $this->assertDatabaseMissing('articles', ['id' => $article->id]);
  }

  /** @test */
  public function unauthorized_user_cannot_access_article_pages()
  {
    $article = Article::factory()->create();

    $this->get('/dashboard/articles/')->assertRedirect('/login');
    $this->get('/dashboard/articles/create')->assertRedirect('/login');
    $this->get('/dashboard/articles/delete/' . $article->id)->assertRedirect('/login');
    $this->post('/dashboard/articles')->assertRedirect('/login');
    $this->put("/dashboard/articles/{$article->id}")->assertRedirect('/login');
    $this->delete('/dashboard/articles/' . $article->id)->assertRedirect('/login');
  }
}
