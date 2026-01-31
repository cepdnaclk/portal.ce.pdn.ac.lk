<?php

namespace App\Domains\ContentManagement\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleContentImageService
{
  public function store(UploadedFile $file, int $tenantId): array
  {
    $disk = config('gallery.disk', 'public');
    $extension = $file->getClientOriginalExtension() ?: $file->extension();
    $imageId = (string) Str::uuid();
    $filename = "{$imageId}.{$extension}";
    $path = $file->storePubliclyAs('articles', $filename, $disk);

    if (! $path) {
      throw new \RuntimeException('Failed to store content image.');
    }

    return [
      'id' => $imageId,
      'disk' => $disk,
      'path' => $path,
      'extension' => $extension,
    ];
  }

  public function deleteImages(array $images): void
  {
    foreach ($images as $image) {
      $path = $image->path ?? ($image['path'] ?? null);
      $disk = $image->disk ?? ($image['disk'] ?? config('gallery.disk', 'public'));

      if (! $path || ! Str::startsWith($path, 'articles/')) {
        continue;
      }

      if (Str::contains($path, '..')) {
        continue;
      }
      if ($path && Storage::disk($disk)->exists($path)) {
        Storage::disk($disk)->delete($path);
      }
    }
  }

  public function filterImagesByContent(array $images, string $content): array
  {
    if (! $images) {
      return [];
    }

    $urls = $this->extractImageUrls($content);
    if (! $urls)  return [];

    return array_values(array_filter($images, function ($image) use ($urls) {
      $url = $image->url ?? null;
      return $url && in_array($url, $urls, true);
    }));
  }

  public function diffImages(array $original, array $kept): array
  {
    $keptIds = array_map(fn($image) => $image->id ?? null, $kept);

    return array_values(array_filter($original, function ($image) use ($keptIds) {
      $id = $image->id ?? null;
      return $id && ! in_array($id, $keptIds, true);
    }));
  }

  public function normalizeImages($images): array
  {
    if (! is_array($images)) {
      return [];
    }

    return array_values(array_filter(array_map(function ($image) {
      // Unwrap payloads like {"stdClass": {...}} back to the actual image object.
      if (is_object($image)) {
        if (isset($image->stdClass) && is_object($image->stdClass)) {
          return $image->stdClass;
        }

        return $image;
      }

      if (is_array($image)) {
        if (array_key_exists('stdClass', $image)) {
          $stdClass = $image['stdClass'];

          return is_object($stdClass) ? $stdClass : (is_array($stdClass) ? (object) $stdClass : null);
        }

        return (object) $image;
      }

      return null;
    }, $images)));
  }

  private function extractImageUrls(string $content): array
  {
    $content = trim($content);
    if ($content === '') {
      return [];
    }

    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($content);
    libxml_clear_errors();

    $urls = [];
    foreach ($dom->getElementsByTagName('img') as $img) {
      $src = $img->getAttribute('src');
      if ($src) {
        $urls[] = $src;
      }
    }

    return array_values(array_unique($urls));
  }
}
