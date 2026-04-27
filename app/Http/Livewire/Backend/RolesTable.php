<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;

/**
 * Class RolesTable.
 */
class RolesTable extends PersistentStateDataTable
{
    protected $model = Role::class;

    public function configure(): void
    {
        parent::configure();
    }

    /**
     * @return Builder
     */
    public function builder(): Builder
    {
        return Role::with('permissions:id,name,description')
            ->withCount('users')
            ->when($this->getSearch(), fn ($query, $term) => $query->search($term));
    }

    public function columns(): array
    {
        return [
            Column::make(__('Type'))
                ->sortable()
                ->format(function ($value, Role $role) {
                    return match ($role->type) {
                        User::TYPE_ADMIN => __('Administrator'),
                        User::TYPE_USER => __('User'),
                        default => 'N/A',
                    };
                }),
            Column::make(__('Name'))
                ->sortable()
                ->searchable(),
            Column::make(__('Permissions'), 'id')
                ->excludeFromColumnSelect()
                ->format(fn($value, Role $role) => new HtmlString($role->permissions_label))
                ->html(),
            Column::make(__('Number of Users'))
                ->label(fn (Role $role) => $role->users_count)
                ->sortable(fn ($query, $direction) => $query->orderBy('users_count', $direction))
                ->excludeFromColumnSelect(),
            Column::make(__('Actions'), 'id')
                ->excludeFromColumnSelect()
                ->format(fn($value, Role $role) => view('backend.auth.role.includes.actions', ['model' => $role]))
                ->html(),
        ];
    }
}
