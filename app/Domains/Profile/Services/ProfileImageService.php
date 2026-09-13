<?php

namespace App\Domains\Profile\Services;

use App\Domains\Profile\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProfileImageService
{
  public function replace(UserProfile $profile, UploadedFile $file): UserProfile
  {
    return $this->store($profile, Image::make($file->getRealPath()));
  }

  /**
   * Download an externally-hosted profile picture (e.g. from profiles:sync)
   * and store it the same way a manual upload is stored.
   *
   * @throws \RuntimeException if the download fails
   */
  public function replaceFromUrl(UserProfile $profile, string $url): UserProfile
  {
    $response = Http::timeout(15)->get($url);

    if (! $response->successful()) {
      throw new \RuntimeException("Failed to download profile image from $url (HTTP {$response->status()}).");
    }

    return $this->store($profile, Image::make($response->body()));
  }

  private function store(UserProfile $profile, $image): UserProfile
  {
    $disk = Storage::disk(config('profile.image.disk'));
    $path = config('profile.image.storage_path') . '/' . Str::uuid() . '.jpg';
    $oldPath = $this->uploadedPath($profile->profile_image);

    $maxDimension = config('profile.image.max_dimension');
    $image->resize($maxDimension, $maxDimension, function ($constraint) {
      $constraint->aspectRatio();
      $constraint->upsize();
    });

    if (! $disk->put($path, (string) $image->encode('jpg', config('profile.image.quality')))) {
      throw new \RuntimeException(__('Failed to store the profile picture.'));
    }

    try {
      $profile->update(['profile_image' => basename($path)]);
    } catch (\Throwable $exception) {
      $disk->delete($path);
      throw $exception;
    }

    if ($oldPath && $oldPath !== $path) {
      $disk->delete($oldPath);
    }

    return $profile->refresh();
  }

  public function delete(UserProfile $profile): void
  {
    $path = $this->uploadedPath($profile->profile_image);

    if ($path) {
      Storage::disk(config('profile.image.disk'))->delete($path);
    }
  }

  private function uploadedPath(?string $url): ?string
  {
    if (! $url) {
      return null;
    }

    $path = parse_url($url, PHP_URL_PATH);
    $filename = basename($path ?: '');
    $uploadedPath = config('profile.image.storage_path') . '/' . $filename;
    $isStoredFileName = $url === $filename;
    $isLegacyStorageUrl = Str::endsWith(ltrim($path ?: '', '/'), $uploadedPath);

    if (
      ! preg_match('/^[0-9a-f-]{36}\.jpg$/i', $filename)
      || (! $isStoredFileName && ! $isLegacyStorageUrl)
    ) {
      return null;
    }

    return $uploadedPath;
  }
}
