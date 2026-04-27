<x-livewire-tables::table.td>
    @if ($row->type === \App\Domains\Auth\Models\User::TYPE_ADMIN)
        {{ __('Administrator') }}
    @elseif ($row->type === \App\Domains\Auth\Models\User::TYPE_USER)
        {{ __('User') }}
    @else
        N/A
    @endif
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    {{ $row->name }}
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    {!! $row->permissions_label !!}
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    {{ $row->users_count }}
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    @include('backend.auth.role.includes.actions', ['model' => $row])
</x-livewire-tables::table.td>
