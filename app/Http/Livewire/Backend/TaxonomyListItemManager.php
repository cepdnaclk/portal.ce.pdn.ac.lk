<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class TaxonomyListItemManager extends Component
{
  public $items = [];
  public $type;
  public $title;
  public $files = [];
  public $pages = [];
  public $articles = [];

  public function mount($type, $title, $items, $files = [], $pages = [], $articles = [])
  {
    $this->items = $items ?? [];
    $this->type = $type;
    $this->title = $title;
    $this->files = $files;
    $this->pages = $pages;
    $this->articles = $articles;
  }

  public function render()
  {
    return view('livewire.backend.taxonomy-list-item-manager');
  }
}
