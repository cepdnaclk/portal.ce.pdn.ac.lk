<?php

namespace Tests\Unit\Services;

use App\Domains\Gallery\Models\GalleryImage;
use App\Domains\Gallery\Services\GalleryService;
use App\Domains\ContentManagement\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryServiceTest extends TestCase
{
  use RefreshDatabase;

  protected $galleryService;

  public function setUp(): void
  {
    parent::setUp();
    $this->galleryService = new GalleryService();
  }

  /** @test */
  public function it_can_upload_a_single_image()
  {
    Storage::fake('public');
    $news = News::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg', 400, 400);

    $images = $this->galleryService->uploadImages($news, [$file]);

    $this->assertCount(1, $images);
    $this->assertInstanceOf(GalleryImage::class, $images[0]);
    $this->assertEquals($news->id, $images[0]->imageable_id);
    $this->assertEquals(News::class, $images[0]->imageable_type);
    $this->assertTrue($images[0]->is_cover);
  }

  /** @test */
  public function it_can_upload_multiple_images()
  {
    Storage::fake('public');
    $news = News::factory()->create();
    $files = [
      UploadedFile::fake()->image('test1.jpg', 400, 400),
      UploadedFile::fake()->image('test2.jpg', 500, 500),
    ];

    $images = $this->galleryService->uploadImages($news, $files);

    $this->assertCount(2, $images);
    $this->assertEquals(0, $images[0]->order);
    $this->assertEquals(1, $images[1]->order);
  }

  /** @test */
  public function first_uploaded_image_is_set_as_cover()
  {
    Storage::fake('public');
    $news = News::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg', 400, 400);

    $images = $this->galleryService->uploadImages($news, [$file]);

    $this->assertTrue($images[0]->is_cover);
  }

  /** @test */
  public function it_can_update_image_metadata()
  {
    Storage::fake('public');
    $image = GalleryImage::factory()->create();

    $updatedImage = $this->galleryService->updateImage($image, [
      'alt_text' => 'New alt text',
      'caption' => 'New caption',
      'credit' => 'New credit',
    ]);

    $this->assertEquals('New alt text', $updatedImage->alt_text);
    $this->assertEquals('New caption', $updatedImage->caption);
    $this->assertEquals('New credit', $updatedImage->credit);
  }

  /** @test */
  public function it_can_reorder_images()
  {
    Storage::fake('public');
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

    $this->galleryService->reorderImages($news, [$image2->id, $image1->id]);

    $image1->refresh();
    $image2->refresh();

    $this->assertEquals(1, $image1->order);
    $this->assertEquals(0, $image2->order);
  }

  /** @test */
  public function it_can_set_cover_image()
  {
    Storage::fake('public');
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

    $this->galleryService->setCoverImage($news, $image2->id);

    $image1->refresh();
    $image2->refresh();

    $this->assertFalse($image1->is_cover);
    $this->assertTrue($image2->is_cover);
  }

  /** @test */
  public function only_one_image_can_be_cover()
  {
    Storage::fake('public');
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

    $image3 = GalleryImage::factory()->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
      'is_cover' => false,
    ]);

    $this->galleryService->setCoverImage($news, $image3->id);

    $coverImages = $news->gallery()->where('is_cover', true)->count();
    $this->assertEquals(1, $coverImages);
  }

  /** @test */
  public function deleting_cover_image_promotes_another()
  {
    Storage::fake('public');
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

    $this->galleryService->deleteImage($image1);

    $image2->refresh();
    $this->assertTrue($image2->is_cover);
  }

  /** @test */
  public function it_provides_gallery_stats()
  {
    Storage::fake('public');
    $news = News::factory()->create();

    GalleryImage::factory()->count(3)->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
    ]);

    $stats = $this->galleryService->getGalleryStats($news);

    $this->assertEquals(3, $stats['total_images']);
    $this->assertArrayHasKey('total_size', $stats);
    $this->assertArrayHasKey('max_images', $stats);
    $this->assertArrayHasKey('can_add_more', $stats);
    $this->assertTrue($stats['can_add_more']);
  }

  /** @test */
  public function it_indicates_when_max_images_reached()
  {
    Storage::fake('public');
    $news = News::factory()->create();

    $maxImages = config('gallery.max_images');
    GalleryImage::factory()->count($maxImages)->create([
      'imageable_type' => News::class,
      'imageable_id' => $news->id,
    ]);

    $stats = $this->galleryService->getGalleryStats($news);

    $this->assertEquals($maxImages, $stats['total_images']);
    $this->assertFalse($stats['can_add_more']);
  }
}
