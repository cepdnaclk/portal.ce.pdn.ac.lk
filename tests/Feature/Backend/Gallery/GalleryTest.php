<?php

namespace Tests\Feature\Backend\Gallery;

use App\Domains\ContentManagement\Models\News;
use App\Domains\ContentManagement\Models\Event;
use App\Domains\Gallery\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function an_editor_can_access_the_gallery_management_page_for_news()
  {
    $this->loginAsEditor();
    $news = News::factory()->create();

    $response = $this->get(route('dashboard.news.gallery.index', $news));
    $response->assertOk();
  }

  /** @test */
  public function an_editor_can_access_the_gallery_management_page_for_events()
  {
    $this->loginAsEditor();
    $event = Event::factory()->create();

    $response = $this->get(route('dashboard.event.gallery.index', $event));
    $response->assertOk();
  }

  /** @test */
  public function an_editor_can_upload_images_to_news_gallery()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();

    $response = $this->post(route('dashboard.news.gallery.upload', $news), [
      'images' => [
        UploadedFile::fake()->image('test1.jpg', 400, 400),
        UploadedFile::fake()->image('test2.jpg', 500, 500),
      ],
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseCount('gallery_images', 2);

    // First image should be set as cover
    $this->assertDatabaseHas('gallery_images', [
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'is_cover' => true,
    ]);
  }

  /** @test */
  public function an_editor_can_upload_images_to_event_gallery()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $event = Event::factory()->create();

    $response = $this->post(route('dashboard.event.gallery.upload', $event), [
      'images' => [
        UploadedFile::fake()->image('test1.jpg', 400, 400),
      ],
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseCount('gallery_images', 1);
  }

  /** @test */
  public function uploading_images_fails_with_invalid_mime_type()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();

    $response = $this->post(route('dashboard.news.gallery.upload', $news), [
      'images' => [
        UploadedFile::fake()->image('test.png'),
      ],
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors();
  }

  /** @test */
  public function uploading_images_fails_when_max_limit_reached()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();

    // Create max number of images
    $maxImages = config('gallery.max_images');
    for ($i = 0; $i < $maxImages; $i++) {
      GalleryImage::factory()->create([
        'imageable_type' => News::class,
        'imageable_id' => $news->id,
      ]);
    }

    $response = $this->post(route('dashboard.news.gallery.upload', $news), [
      'images' => [
        UploadedFile::fake()->image('test.jpg', 400, 400),
      ],
    ]);

    $response->assertStatus(422);
    $response->assertJson(['message' => 'Maximum number of images reached']);
  }

  /** @test */
  public function an_editor_can_update_image_metadata()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();
    $image = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
    ]);

    $response = $this->put(route('dashboard.gallery.update', $image), [
      'alt_text' => 'New alt text',
      'caption' => 'New caption',
      'credit' => 'Photographer Name',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('gallery_images', [
      'id' => $image->id,
      'alt_text' => 'New alt text',
      'caption' => 'New caption',
      'credit' => 'Photographer Name',
    ]);
  }

  /** @test */
  public function an_editor_can_set_an_image_as_cover()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();

    $image1 = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'is_cover' => true,
    ]);

    $image2 = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'is_cover' => false,
    ]);

    $response = $this->put(route('dashboard.news.gallery.set-cover', [$news, $image2]));

    $response->assertStatus(200);

    // Check that only image2 is now cover
    $this->assertDatabaseHas('gallery_images', [
      'id' => $image2->id,
      'is_cover' => true,
    ]);

    $this->assertDatabaseHas('gallery_images', [
      'id' => $image1->id,
      'is_cover' => false,
    ]);
  }

  /** @test */
  public function an_editor_can_reorder_gallery_images()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();

    $image1 = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'order' => 0,
    ]);

    $image2 = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'order' => 1,
    ]);

    $response = $this->post(route('dashboard.news.gallery.reorder', $news), [
      'ordered_ids' => [$image2->id, $image1->id],
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('gallery_images', [
      'id' => $image2->id,
      'order' => 0,
    ]);

    $this->assertDatabaseHas('gallery_images', [
      'id' => $image1->id,
      'order' => 1,
    ]);
  }

  /** @test */
  public function an_editor_can_delete_a_gallery_image()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();

    $image = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
    ]);

    $response = $this->delete(route('dashboard.gallery.destroy', $image));

    $response->assertStatus(200);
    $this->assertSoftDeleted('gallery_images', ['id' => $image->id]);
  }

  /** @test */
  public function deleting_cover_image_promotes_another_image()
  {
    Storage::fake('public');
    $this->loginAsEditor();
    $news = News::factory()->create();

    $image1 = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'is_cover' => true,
      'order' => 0,
    ]);

    $image2 = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'is_cover' => false,
      'order' => 1,
    ]);

    $this->delete(route('dashboard.gallery.destroy', $image1));

    // Image2 should now be cover
    $this->assertDatabaseHas('gallery_images', [
      'id' => $image2->id,
      'is_cover' => true,
    ]);
  }

  /** @test */
  public function unauthorized_user_cannot_access_gallery_management()
  {
    $news = News::factory()->create();

    $this->get(route('dashboard.news.gallery.index', $news))->assertRedirect('/login');
    $this->post(route('dashboard.news.gallery.upload', $news))->assertRedirect('/login');
  }
}