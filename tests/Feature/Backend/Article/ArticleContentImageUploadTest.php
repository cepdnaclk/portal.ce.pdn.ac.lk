<?php

namespace Tests\Feature\Backend\Article;

use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleContentImageUploadTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function article_content_images_can_be_uploaded()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $tenantId = Tenant::defaultId();

    $file = UploadedFile::fake()->image('sample.jpg', 400, 400)->mimeType('image/jpeg');

    $response = $this->postJson('/dashboard/articles/content-images/upload', [
      'image' => $file,
      'tenant_id' => $tenantId,
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure(['location', 'id', 'url', 'path']);

    $path = $response->json('path');
    Storage::disk('public')->assertExists($path);
  }
}