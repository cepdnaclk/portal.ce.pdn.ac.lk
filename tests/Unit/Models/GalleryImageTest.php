<?php

namespace Tests\Unit\Models;

use App\Domains\Gallery\Models\GalleryImage;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;
use Tests\CreatesApplication;

class GalleryImageTest extends BaseTestCase
{
  use CreatesApplication;

  public function setUp(): void
  {
    putenv('CACHE_DRIVER=array');
    $_ENV['CACHE_DRIVER'] = 'array';
    $_SERVER['CACHE_DRIVER'] = 'array';

    parent::setUp();

    config(['cache.default' => 'array']);
  }

  /** @test */
  public function it_falls_back_to_public_gallery_path_when_storage_file_is_missing()
  {
    Storage::fake('public');

    $image = new GalleryImage([
      'disk' => 'public',
      'path' => 'img/gallery/sample.jpg',
    ]);

    $legacyDir = public_path('img/gallery');
    $createdDir = false;

    if (! is_dir($legacyDir)) {
      mkdir($legacyDir, 0777, true);
      $createdDir = true;
    }

    $legacyThumbPath = $legacyDir . '/sample_thumb.jpg';
    file_put_contents($legacyThumbPath, 'test');

    clearstatcache();
    $this->assertFileExists($legacyThumbPath);
    $this->assertFalse(Storage::disk('public')->exists('img/gallery/sample_thumb.jpg'));
    $this->assertFalse(Storage::disk('public')->exists('img/gallery/sample.jpg'));

    $this->assertSame(
      asset('img/gallery/sample_thumb.jpg'),
      $image->getSizeUrl('thumb')
    );

    @unlink($legacyThumbPath);

    if ($createdDir) {
      @rmdir($legacyDir);
    }
  }
}