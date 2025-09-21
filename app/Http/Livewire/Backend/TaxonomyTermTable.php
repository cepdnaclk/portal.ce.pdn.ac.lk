<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class TaxonomyTermTable extends DataTableComponent
{
    public array $perPageAccepted = [10, 25, 50, 100];
    public int $perPage = 100;
    public bool $perPageAll = true;

    public string $defaultSortColumn = 'code';
    public string $defaultSortDirection = 'asc';


    public $taxonomy;

    public function mount($taxonomy)
    {
        $this->taxonomy = $taxonomy;

        // If there are no query parameters, attempt to restore state from cookie
        if (empty(request()->query())) {
            $this->restoreStateFromCookie();
        }

        // Persist current state to ensure cookie stays in sync
        $this->persistStateToCookie();
    }

    // Persist filters/search/pagination to a browser cookie
    protected function persistStateToCookie(): void
    {
        $state = [
            'filters' => $this->getFilters(),
            'page' => $this->{ $this->pageName() } ?? 1,
            'perPage' => $this->perPage,
        ];
        Cookie::queue($this->getCookieKey(), json_encode($state), 0);
    }

    // Restore filters/search/pagination from cookie, if available
    protected function restoreStateFromCookie(): void
    {
        $raw = Cookie::get($this->getCookieKey());
        if (! $raw) {
            return;
        }

        $data = json_decode($raw, true);
        if (! is_array($data)) return;

        // Restore filters (including search) if present
        if (isset($data['filters']) && is_array($data['filters'])) {
            // Merge into existing to keep base filters like 'search' initialized
            $this->filters = array_merge($this->filters, $data['filters']);
            // Ensure empty/invalid filters are cleaned
            if (method_exists($this, 'checkFilters')) {
                $this->checkFilters();
            }
        }

        // Restore per-page if valid
        if (isset($data['perPage']) && in_array((int) $data['perPage'], $this->perPageAccepted, true)) {
            $this->perPage = (int) $data['perPage'];
        }

        // Restore current page
        if (isset($data['page']) && is_numeric($data['page'])) {
            $pageNumber = max(1, (int) $data['page']);
            $this->gotoPage($pageNumber);
        }
    }

    protected function getCookieKey(): string
    {
        $taxonomyId = is_object($this->taxonomy) && isset($this->taxonomy->id) ? $this->taxonomy->id : 'unknown';
        return "taxonomy_terms_{$taxonomyId}";
    }

    // Listen for any property updates and persist state
    public function updated($name, $value): void
    {
        // Only persist when relevant pieces change
        $isFilters = substr($name, 0, 7) === 'filters';
        if ($name === 'perPage' || $name === $this->pageName() || $isFilters) {
            $this->persistStateToCookie();
        }
    }

    public function columns(): array
    {
        return [
            Column::make("Name", "name")
                ->searchable()->sortable(),
            Column::make("Code", "code")
                ->searchable()->sortable(),
            Column::make("Taxonomy Parent", "parent_id"),
            Column::make("Created by", "created_by")
                ->sortable(),
            Column::make("Updated by", "updated_by")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make("API"),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return TaxonomyTerm::query()
            ->where('taxonomy_id', $this->taxonomy->id)
            ->when($this->getFilter('taxonomy_term'), fn($query, $type) => $query->where('parent_id', $type)->orWhere('id', $type))
            ->with('user')->orderBy('parent_id');
    }

    public function filters(): array
    {
        $terms = [];
        foreach (
            TaxonomyTerm::query()
                ->where('taxonomy_id', $this->taxonomy->id)->get() as $key => $value
        ) {
            $terms[$value->id] = $value->name;
        };

        return [
            'taxonomy_term' => Filter::make('Taxonomy Term')
                ->select($terms)
        ];
    }


    public function rowView(): string
    {
        return 'backend.taxonomy.terms.index-table-row';
    }
}