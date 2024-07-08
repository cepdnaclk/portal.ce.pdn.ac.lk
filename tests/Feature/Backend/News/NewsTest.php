<?php

namespace Tests\Feature\Backend\News;

use App\Domains\News\Models\News;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_news_editor_can_access_the_list_news_page()
    {
        $this->loginAsNewsEditor();
        $this->get('/dashboard/news/')->assertOk();
    }

    /** @test */
    public function a_news_editor_can_access_the_create_news_page()
    {
        $this->loginAsNewsEditor();
        $this->get('/dashboard/news/create')->assertOk();
    }

    /** @test */
    public function a_news_editor_can_access_the_delete_news_page()
    {
        $this->loginAsNewsEditor();
        $news = News::factory()->create();
        $this->get('/dashboard/news/delete/' . $news->id)->assertOk();
    }

    /** @test *//*
    public function create_news_requires_validation()
    {
        $this->loginAsNewsEditor();
        $response = $this->post('/dashboard/news');
        $response->assertSessionHasErrors(['title', 'type', 'description', 'image', 'link_url', 'link_caption']);
    }
*/
    /** @test */
    /*  public function update_news_requires_validation()
    {
        $this->loginAsNewsEditor();
        $news = News::factory()->create();

        $response = $this->put("/dashboard/news/{$news->id}", []);
        $response->assertSessionHasErrors(['title', 'type', 'description', 'image', 'link_url', 'link_caption']);
    }
*/
    /** @test */
    public function news_can_be_created()
    {
        $this->loginAsNewsEditor();
        $response = $this->post('/dashboard/news/', [
            'title' => 'test News',
            'description' => 'This is a sample news description.',
            'author' => 'John Doe',
            'image' => 'sample-image.jpg',
            'link_url' => 'http://example.com',
            'link_caption' => 'Example Link',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('news', [
            'title' => 'test News',
        ]);
    }

    /** @test */
    public function news_can_be_updated()
    {
        $this->loginAsNewsEditor();
        $news = News::factory()->create();

        $updateData = [
            'title' => 'Updated News',
            'description' => 'This is an updated news description.',
            'author' => 'ishara',
            'image' => 'https:\/\/via.placeholder.com\/640x480.png\/000055?text=quia',
            'link_url' => 'http://example.com',
            'link_caption' => 'eaque excepturi velit',
        ];

        $response = $this->put("/dashboard/news/{$news->id}", $updateData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('news', [
            'title' => 'Updated News',
        ]);
    }

    /** @test */
    public function news_can_be_deleted()
    {
        $this->loginAsNewsEditor();
        $news = News::factory()->create();
        $this->delete('/dashboard/news/' . $news->id);
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    /** @test */
    public function unauthorized_user_cannot_access_news_pages()
    {
        $news = News::factory()->create();

        $this->get('/dashboard/news/')->assertRedirect('/login');
        $this->get('/dashboard/news/create')->assertRedirect('/login');
        $this->get('/dashboard/news/delete/' . $news->id)->assertRedirect('/login');
        $this->post('/dashboard/news')->assertRedirect('/login');
        $this->put("/dashboard/news/{$news->id}")->assertRedirect('/login');
        $this->delete('/dashboard/news/' . $news->id)->assertRedirect('/login');
    }
}
