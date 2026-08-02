<?php

namespace App\Http\Controllers\Concerns;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait StreamsFiles
{
  protected function streamFile(string $absolutePath, int $cacheTtl = 31536000): BinaryFileResponse
  {
    $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

    return response()->file($absolutePath, [
      'Content-Type' => $mime,
      'Cache-Control' => 'public, max-age=' . $cacheTtl,
    ]);
  }
}