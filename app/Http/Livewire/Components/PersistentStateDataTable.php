<?php

namespace App\Http\Livewire\Components;

use Illuminate\Support\Facades\Cookie;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

/**
 * Base DataTable component that persists filters/search/pagination to a cookie
 * and restores them when the page is loaded with no query params.
 */
abstract class PersistentStateDataTable extends DataTableComponent
{
    /**
     * Which parts of the state to persist.
     * Supported keys: filters, perPage, page, sorts
     *
     * @var array
     */
    protected array $persistedStateKeys = ['filters', 'perPage', 'page'];

    /**
     * Hook after the child component's mount() has run.
     * We restore the cookie state only if there are no query params.
     */
    public function booted(): void
    {
        if ($this->shouldRestoreStateFromCookie()) {
            $this->restoreStateFromCookie();
        }

        // Always persist once during the initial request to keep cookie up to date
        $this->persistStateToCookie();
    }

    /**
     * Persist when relevant properties change.
     */
    public function updated($name, $value): void
    {
        $pageName = $this->pageName();
        $isFilters = substr($name, 0, 7) === 'filters';
        $isPerPage = $name === 'perPage';
        $isPage = $name === $pageName;
        $isSorts = $name === 'sorts';

        if ($isFilters || $isPerPage || $isPage || $isSorts) {
            $this->persistStateToCookie();
        }
    }

    /*
    * Override resetFilters to persist after resetting.
    */
    public function resetFilters(): void
    {
        parent::resetFilters();
        $this->persistStateToCookie();
    }

    /**
     * Override setPage to persist after changing page.
     */
    public function setPage($page, $pageName = 'page'){
        parent::setPage($page, $pageName);
        $this->persistStateToCookie();
    }

    /**
     * Determine if we should attempt to restore from cookie.
     * Default: only when there are no query parameters present.
     */
    protected function shouldRestoreStateFromCookie(): bool
    {
        return empty(request()->query());
    }

    /**
     * Save configured state to cookie as JSON.
     */
    protected function persistStateToCookie(): void
    {
        $state = [];

        if (in_array('filters', $this->persistedStateKeys, true)) {
            $filters = method_exists($this, 'getFilters') ? $this->getFilters() : ($this->filters ?? []);
            // Drop cleared/empty filter values so cookie doesn't retain them
            if (is_array($filters)) {
                $filters = $this->sanitizeFilters($filters);
            }
            $state['filters'] = $filters;
        }

        if (in_array('perPage', $this->persistedStateKeys, true) && property_exists($this, 'perPage')) {
            $state['perPage'] = $this->perPage;
        }

        if (in_array('page', $this->persistedStateKeys, true)) {
            $state['page'] = $this->{ $this->pageName() } ?? 1;
        }

        if (in_array('sorts', $this->persistedStateKeys, true) && property_exists($this, 'sorts')) {
            $state['sorts'] = $this->sorts;
        }

        Cookie::queue($this->getCookieKey(), json_encode($state), 60 * 24 * 30);
    }

    /**
     * Remove null/empty filter values recursively for clean persistence.
     */
    protected function sanitizeFilters(array $filters): array
    {
        $clean = [];
        foreach ($filters as $key => $val) {
            if (is_array($val)) {
                $val = $this->sanitizeFilters($val);
                if ($val === []) {
                    continue;
                }
            } else {
                if ($val === null || $val === '' || $val === false) {
                    continue;
                }
            }
            $clean[$key] = $val;
        }
        return $clean;
    }

    /**
     * Restore the persisted state from cookie.
     */
    protected function restoreStateFromCookie(): void
    {
        $raw = Cookie::get($this->getCookieKey());
        if (! $raw) {
            return;
        }

        $data = json_decode($raw, true);
        if (! is_array($data)) {
            return;
        }

        if (isset($data['filters']) && is_array($data['filters']) && in_array('filters', $this->persistedStateKeys, true)) {
            $this->filters = array_merge($this->filters ?? [], $data['filters']);
            if (method_exists($this, 'checkFilters')) {
                $this->checkFilters();
            }
        }

        if (isset($data['perPage']) && in_array('perPage', $this->persistedStateKeys, true) && property_exists($this, 'perPage')) {
            // Validate perPage against accepted values if available
            if (property_exists($this, 'perPageAccepted')) {
                if (in_array((int) $data['perPage'], $this->perPageAccepted, true)) {
                    $this->perPage = (int) $data['perPage'];
                }
            } else {
                $this->perPage = (int) $data['perPage'];
            }
        }

        if (isset($data['sorts']) && in_array('sorts', $this->persistedStateKeys, true) && property_exists($this, 'sorts')) {
            if (is_array($data['sorts'])) {
                $this->sorts = $data['sorts'];
            }
        }

        if (isset($data['page']) && in_array('page', $this->persistedStateKeys, true)) {
            $pageNumber = max(1, (int) $data['page']);
            $this->gotoPage($pageNumber);
        }
      }

    /**
     * Build a stable cookie key that can be overridden per component.
     */
    protected function getCookieKey(): string
    {
        $base = class_basename(static::class);
        $context = $this->getCookieContextKey();
        return $context ? "datatable_state_{$base}_{$context}" : "datatable_state_{$base}";
    }

    /**
     * Optional context to differentiate pages (e.g., taxonomy id).
     */
    protected function getCookieContextKey(): string
    {
        return '';
    }
}