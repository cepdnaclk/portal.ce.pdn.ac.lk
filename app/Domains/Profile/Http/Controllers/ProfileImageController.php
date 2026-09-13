<?php

namespace App\Domains\Profile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileImageController extends Controller
{
  public function download(string $fileName): BinaryFileResponse
  {
    $fileName = basename($fileName);

    if (! preg_match('/^[0-9a-f-]{36}\.jpg$/i', $fileName)) {
      return abort(404, 'File not found.');
    }

    $disk = Storage::disk(config('profile.image.disk'));
    $path = config('profile.image.storage_path') . '/' . $fileName;

    if (! $disk->exists($path)) {
      return abort(404, 'File not found.');
    }

    return response()->file($disk->path($path), [
      'Content-Type' => 'image/jpeg',
      'Cache-Control' => 'public, max-age=' . config('profile.image.cache_ttl'),
    ]);
  }
}
