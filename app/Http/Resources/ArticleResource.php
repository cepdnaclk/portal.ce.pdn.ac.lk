<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'content' => $this->content,
      'author' => [
        'name' => $this->author?->name ?? '',
        'email' => $this->author?->email ?? '',
        'profile_url' => '#',
      ],
      'published_at' => $this->published_at,
      'categories' => $this->categories_json ?? [],
      'content_images' => collect($this->content_images_json ?? [])
        ->map(function ($image) {
          return [
            'id' => $image['id'] ?? null,
            'url' => $image['url'] ?? null,
          ];
        })
        ->filter(fn($image) => $image['id'] || $image['url'])
        ->values(),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'gallery' => config('gallery.enabled') ? GalleryImageResource::collection($this->whenLoaded('gallery')) : [],
    ];
  }
}