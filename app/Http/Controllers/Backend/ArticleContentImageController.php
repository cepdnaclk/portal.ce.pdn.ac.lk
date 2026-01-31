<?php

namespace App\Http\Controllers\Backend;

use App\Domains\ContentManagement\Services\ArticleContentImageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Articles\UploadArticleContentImageRequest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;

class ArticleContentImageController extends Controller
{
  public function __construct(private ArticleContentImageService $contentImageService) {}

  public function upload(UploadArticleContentImageRequest $request)
  {
    $tenant = $request->attributes->get('tenant');

    if (! $tenant || (int) $request->input('tenant_id') !== (int) $tenant->id) {
      return response()->json(['message' => 'Tenant not found'], 404);
    }

    try {
      $image = $this->contentImageService->store($request->file('image'), $tenant->id);

      $downloadPath = basename($image['path']);
      $location = route('download.article', ['path' => $downloadPath], true);
      return response()->json([
        'location' => $location,
        'id' => $image['id'],
        'url' => $location,
        'path' => $image['path'],
        'disk' => $image['disk'],
      ], 201);
    } catch (\Exception $ex) {
      Log::error('Failed to upload article content image', [
        'tenant_id' => $tenant?->id,
        'user_id' => $request->user()?->id,
        'error' => $ex->getMessage(),
      ]);

      return response()->json(['message' => 'Failed to upload image'], 500);
    }
  }

  public function download(string $path): BinaryFileResponse
  {
    $diskName = config('gallery.disk', 'public');
    $disk = Storage::disk($diskName);
    $fileName = basename($path);
    if ($fileName === '') {
      return abort(404, 'File not found.');
    }
    $filePath = "articles/{$fileName}";
    if (!$disk->exists($filePath)) {
      return abort(404, 'File not found.');
    }
    return $this->streamFile($disk->path($filePath));
  }

  protected function streamFile(string $absolutePath): BinaryFileResponse
  {
    $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

    return response()->file($absolutePath, [
      'Content-Type' => $mime,
      'Cache-Control' => 'public, max-age=' . config('gallery.cache_ttl', 31536000),
    ]);
  }
}
