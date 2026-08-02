<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Concerns\StreamsFiles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileImageController extends Controller
{
  use StreamsFiles;

  public function download(string $path): BinaryFileResponse
  {
    $diskName = config('profiles.image.disk', 'public');
    $disk = Storage::disk($diskName);
    $fileName = basename($path);

    if ($fileName === '') {
      return abort(404, 'File not found.');
    }

    $directory = trim((string) config('profiles.image.directory', 'profiles'), '/');
    $filePath = $directory . '/' . $fileName;

    if (! $disk->exists($filePath)) {
      return abort(404, 'File not found.');
    }

    return $this->streamFile($disk->path($filePath));
  }
}