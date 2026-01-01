<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

/**
 * Class ExpandableInfoCard
 *
 * This Livewire component represents an expandable information card
 * used in the backend of the application. It provides functionality
 * to display and manage expandable content dynamically.
 *
 * @package App\Http\Livewire\Backend
 */
class ExpandableInfoCard extends Component
{
  public string $title;
  public string $description;
  public bool $isExpanded = false;

  public function mount(string $title, string $description): void
  {
    $this->title = $title;
    $this->description = $description;
  }

  public function toggleInfo(): void
  {
    $this->isExpanded = !$this->isExpanded;
  }

  public function render()
  {
    return view('livewire.backend.expandable-info-card');
  }
}