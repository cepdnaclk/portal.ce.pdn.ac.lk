<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class SearchableDropdown extends Component
{
  public string $name;
  public ?string $inputId = null;
  public array $options = [];
  public $selected = null; // string|int|null
  public string $placeholder = 'Select an option';
  public ?string $icon = null; // e.g., 'fa fa-file'

  public string $search = '';
  public bool $open = false;

  public function mount(string $name, array $options = [], $selected = null, string $placeholder = 'Select an option', ?string $icon = null, ?string $inputId = null)
  {
    $this->name = $name;
    $this->options = $options;
    $this->selected = $selected;
    $this->placeholder = $placeholder;
    $this->icon = $icon;
    $this->inputId = $inputId;
  }

  public function select($key): void
  {
    $this->selected = $key === '' ? null : $key;
    $this->open = false;
    $this->emit('searchable-dropdown-updated', [
      'name' => $this->name,
      'value' => $this->selected,
    ]);
  }

  public function clear(): void
  {
    $this->selected = null;
    $this->emit('searchable-dropdown-cleared', [
      'name' => $this->name,
    ]);
  }

  public function toggle(): void
  {
    $this->open = !$this->open;
  }

  public function getSelectedLabelProperty(): string
  {
    if ($this->selected === null || $this->selected === '') {
      return $this->placeholder;
    }
    return (string)($this->options[$this->selected] ?? $this->placeholder);
  }

  public function getFilteredOptionsProperty(): array
  {
    $needle = mb_strtolower(trim($this->search));
    $items = $this->options;

    // Keep any placeholder/empty option at the top if present
    $top = [];
    if (array_key_exists('', $items)) {
      $top[''] = $items[''];
      unset($items['']);
    }

    if ($needle === '') {
      return $top + $items;
    }

    $filtered = [];
    foreach ($items as $key => $label) {
      if (mb_strpos(mb_strtolower((string)$label), $needle) !== false) {
        $filtered[$key] = $label;
      }
    }
    return $top + $filtered;
  }

  public function render()
  {
    return view('livewire.backend.searchable-dropdown');
  }
}