<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

/**
 * Class UsersTable.
 */
class UsersTable extends PersistentStateDataTable
{
    protected $model = User::class;

    /**
     * @var
     */
    public $status;

    /**
     * @var array|string[]
     */
    public array $sortNames = [
        'email_verified_at' => 'Verified',
        'two_factor_auth_count' => '2FA',
    ];

    /**
     * @var array|string[]
     */
    public array $filterNames = [
        'type' => 'User Type',
        'verified' => 'E-mail Verified',
    ];

    public function configure(): void
    {
        parent::configure();

        $this->setPerPage(25);
        $this->setPerPageAccepted([10, 25, 50, 100, -1]);
    }

    /**
     * @param  string  $status
     */
    public function mount($status = 'active'): void
    {
        $this->status = $status;
    }

    /**
     * @return Builder
     */
    public function builder(): Builder
    {
        $query = User::with('roles', 'twoFactorAuth')->withCount('twoFactorAuth');

        if ($this->status === 'deleted') {
            $query = $query->onlyTrashed();
        } elseif ($this->status === 'deactivated') {
            $query = $query->onlyDeactivated();
        } else {
            $query = $query->onlyActive();
        }

        return $query
            ->when($this->getSearch(), fn($query, $term) => $query->search($term))
            ->when($this->getAppliedFilterWithValue('type'), fn($query, $type) => $query->where('type', $type))
            ->when($this->getAppliedFilterWithValue('active'), fn($query, $active) => $query->where('active', $active === 'yes'))
            ->when($this->getAppliedFilterWithValue('verified'), fn($query, $verified) => $verified === 'yes' ? $query->whereNotNull('email_verified_at') : $query->whereNull('email_verified_at'));
    }

    /**
     * @return array
     */
    public function filters(): array
    {
        return [
            SelectFilter::make('User Type', 'type')
                ->options([
                    '' => 'Any',
                    User::TYPE_ADMIN => 'Administrators',
                    User::TYPE_USER => 'Users',
                ]),
            SelectFilter::make('Active', 'active')
                ->options([
                    '' => 'Any',
                    'yes' => 'Yes',
                    'no' => 'No',
                ]),
            SelectFilter::make('E-mail Verified', 'verified')
                ->options([
                    '' => 'Any',
                    'yes' => 'Yes',
                    'no' => 'No',
                ]),
        ];
    }

    /**
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::make(__('Type'), 'type')
                ->sortable()
                ->format(fn($value, User $user) => view('backend.auth.user.includes.type', ['user' => $user]))
                ->html(),
            Column::make(__('Name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('E-mail'), 'email')
                ->sortable()
                ->searchable()
                ->format(fn($value, User $user) => new HtmlString('<a href="mailto:' . e($user->email) . '">' . e($user->email) . '</a>'))
                ->html(),
            Column::make(__('Verified'), 'email_verified_at')
                ->sortable()
                ->format(fn($value, User $user) => view('backend.auth.user.includes.verified', ['user' => $user]))
                ->html(),
            Column::make(__('Roles'), 'id')
                ->excludeFromColumnSelect()
                ->format(fn($value, User $user) => new HtmlString($user->roles_label))
                ->html(),
            Column::make(__('Additional Permissions'), 'id')
                ->excludeFromColumnSelect()
                ->format(fn($value, User $user) => new HtmlString($user->permissions_label))
                ->html(),
            Column::make(__('Actions'), 'id')
                ->excludeFromColumnSelect()
                ->format(fn($value, User $user) => view('backend.auth.user.includes.actions', ['user' => $user]))
                ->html(),
        ];
    }
}
