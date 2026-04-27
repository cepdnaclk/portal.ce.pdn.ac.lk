<?php

namespace App\Http\Livewire\Components;

use Rappasoft\LaravelLivewireTables\DataTableComponent;

/**
 * Base table component with shared configuration defaults.
 */
abstract class PersistentStateDataTable extends DataTableComponent
{
  public function configure(): void
  {
    $this->setPrimaryKey('id');

    // Pagination
    $this->setPaginationStatus(true)
      ->setDefaultPerPage(10)
      ->setPerPageAccepted([10, 25, 50, 100, -1])
      ->setPaginationVisibilityStatus(true);

    // if (property_exists($this, 'perPage')) {
    //   $this->setPerPage($this->perPage);
    // }
    // if (property_exists($this, 'perPageAccepted')) {
    //   $perPageAccepted = $this->perPageAccepted;

    //   if (property_exists($this, 'perPageAll') && $this->perPageAll && ! in_array(-1, $perPageAccepted, true)) {
    //     $perPageAccepted[] = -1;
    //   }
    //   $this->setPerPageAccepted($perPageAccepted);
    // }

    // if (property_exists($this, 'defaultSortColumn') && $this->defaultSortColumn) {
    //   $direction = property_exists($this, 'defaultSortDirection') ? $this->defaultSortDirection : 'asc';

    //   $this->setDefaultSort($this->defaultSortColumn, $direction);
    // }
  }
}