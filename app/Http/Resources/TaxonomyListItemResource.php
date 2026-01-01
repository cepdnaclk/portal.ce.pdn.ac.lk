<?php

namespace App\Http\Resources;

use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxonomyListItemResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   */
  public function toArray($request): array
  {
    $items = array_values($this->items ?? []);

    switch ($this->data_type) {
      case 'file':
        $items = $this->mapFileItems($items);
        break;
      case 'page':
        $items = $this->mapPageItems($items);
        break;
      default:
        // Keep the items as stored for other types (string, date, url, email)
        break;
    }

    return [
      'name' => $this->name,
      'data_type' => $this->data_type,
      'items' => $items,
    ];
  }

  private function mapFileItems(array $itemIds): array
  {
    if (empty($itemIds)) {
      return [];
    }

    $files = TaxonomyFile::whereIn('id', $itemIds)->get()->keyBy('id');

    return collect($itemIds)->map(function ($id) use ($files) {
      $file = $files->get($id);

      if (!$file) {
        return [
          'id' => $id,
          'missing' => true,
        ];
      }

      return [
        'id' => $file->id,
        'name' => $file->file_name,
        'url' => route('download.taxonomy-file', [
          'file_name' => $file->file_name,
          'extension' => $file->getFileExtension(),
        ]),
      ];
    })->all();
  }

  private function mapPageItems(array $itemIds): array
  {
    if (empty($itemIds)) {
      return [];
    }

    $pages = TaxonomyPage::whereIn('id', $itemIds)->get()->keyBy('id');

    return collect($itemIds)->map(function ($id) use ($pages) {
      $page = $pages->get($id);

      if (!$page) {
        return [
          'id' => $id,
          'missing' => true,
        ];
      }

      return [
        'id' => $page->id,
        'slug' => $page->slug,
        'url' => route('download.taxonomy-page', ['slug' => $page->slug]),
      ];
    })->all();
  }
}