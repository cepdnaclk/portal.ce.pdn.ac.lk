<?php

namespace Tests\Feature\Backend\News;

use App\Domains\News\Models\News;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_news_editor_can_access_the_list_news_page()
    {
        $this->loginAsEditor();
        $this->get('/dashboard/news/')->assertOk();
    }

    /** @test */
    public function a_news_editor_can_access_the_create_news_page()
    {
        $this->loginAsEditor();
        $this->get('/dashboard/news/create')->assertOk();
    }

    /** @test */
    public function a_news_editor_can_access_the_delete_news_page()
    {
        $this->loginAsEditor();
        $news = News::factory()->create();
        $this->get('/dashboard/news/delete/' . $news->id)->assertOk();
    }

    /** @test */
    public function news_can_be_created()
    {
        $this->loginAsEditor();
        $response = $this->post('/dashboard/news/', [
            'title' => 'test News',
            'description' => 'This is a sample news description.',
            'image' => UploadedFile::fake()->image('sample.jpg'),
            'link_url' => 'http://example.com',
            'link_caption' => 'Example Link',
            'url' => 'https://ce.pdn.ac.lk/news/2004-10-10',
            'published_at' => '2024-12-12',

        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('news', [
            'title' => 'test News',
        ]);
    }

    /** @test */
    public function news_can_be_updated()
    {
        $this->loginAsEditor();
        $news = News::factory()->create();

        $updateData = [
            'title' => 'Updated News',
            'description' => 'This is an updated news description.',
            'image' => UploadedFile::fake()->image('sample.jpg'),
            'link_url' => 'http://example.com',
            'link_caption' => 'eaque excepturi velit',
            'url' => 'https://ce.pdn.ac.lk/news/2004-11-11',
            'published_at' => '2024-12-24',
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
        $this->loginAsEditor();
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
