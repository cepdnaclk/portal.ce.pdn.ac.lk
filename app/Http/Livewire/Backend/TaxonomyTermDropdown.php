<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\TaxonomyTerm;

class TaxonomyTermDropdown extends SearchableDropdown
{
  public ?string $editUrl = '#';
  protected static array $editUrlCache = [];

  public function mount(string $name, array $options = [], $selected = null, string $placeholder = 'Select an option', ?string $icon = null, ?string $inputId = null)
  {
    parent::mount($name, $options, $selected, $placeholder, $icon, $inputId);
    $this->updateEditUrl();
  }

  public function select($key): void
  {
    parent::select($key);
    $this->updateEditUrl();
  }

  public function clear(): void
  {
    parent::clear();
    $this->updateEditUrl();
  }

  public function render()
  {
    return view('livewire.backend.taxonomy-term-dropdown');
  }

  protected function updateEditUrl(): void
  {
    $selected = $this->selected;

    if ($selected === null || $selected === '') {
      $this->editUrl = '#';
      return;
    }

    $termId = (int)$selected;
    if ($termId <= 0) {
      $this->editUrl = '#';
      return;
    }

    if (!array_key_exists($termId, self::$editUrlCache)) {
      $term = TaxonomyTerm::query()->select(['id', 'code'])->find($termId);
      self::$editUrlCache[$termId] = $term ? route('dashboard.taxonomy.alias', ['code' => $term->code]) : '#';
    }

    $this->editUrl = self::$editUrlCache[$termId];
  }
}