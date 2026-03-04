<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class RoleAssignedUsersTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [10, 25, 50, 100];
  public int $perPage = 25;
  public bool $perPageAll = true;
  public int $roleId;

  /**
   * @var array|string[]
   */
  public array $filterNames = [
    'type' => 'User Type',
    'verified' => 'E-mail Verified',
  ];

  public function mount(Role $role): void
  {
    $this->roleId = $role->getKey();
  }

  public function query(): Builder
  {
    return User::with('roles', 'twoFactorAuth')->withCount('twoFactorAuth')
      ->whereHas('roles', fn($query) => $query->whereKey($this->roleId))
      ->when($this->getFilter('search'), fn($query, $term) => $query->search($term))
      ->when($this->getFilter('type'), fn($query, $type) => $query->where('type', $type))
      ->when($this->getFilter('active'), fn($query, $active) => $query->where('active', $active === 'yes'))
      ->when($this->getFilter('verified'), fn($query, $verified) => $verified === 'yes' ? $query->whereNotNull('email_verified_at') : $query->whereNull('email_verified_at'));
  }

  public function filters(): array
  {
    return [
      'type' => Filter::make('User Type')
        ->select([
          '' => 'Any',
          User::TYPE_ADMIN => 'Administrators',
          User::TYPE_USER => 'Users',
        ]),
      'active' => Filter::make('Active')
        ->select([
          '' => 'Any',
          'yes' => 'Yes',
          'no' => 'No',
        ]),
      'verified' => Filter::make('E-mail Verified')
        ->select([
          '' => 'Any',
          'yes' => 'Yes',
          'no' => 'No',
        ]),
    ];
  }

  public function columns(): array
  {
    return [
      Column::make(__('Type'))->sortable(),
      Column::make(__('Name'))->sortable(),
      Column::make(__('E-mail'), 'email')->sortable(),
      Column::make(__('Verified'), 'email_verified_at')->sortable(),
      Column::make(__('Roles')),
      Column::make(__('Additional Permissions')),
      Column::make(__('Actions')),
    ];
  }

  public function rowView(): string
  {
    return 'backend.auth.user.includes.row';
  }

  protected function getCookieContextKey(): string
  {
    return 'role_' . $this->roleId;
  }
}
