<x-livewire-tables::table.td>
    @include('backend.auth.user.includes.type', ['user' => $row])
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    {{ $row->name }}
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    <a href="mailto:{{ $row->email }}">{{ $row->email }}</a>
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    @include('backend.auth.user.includes.verified', ['user' => $row])
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    {!! $row->roles_label !!}
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    {!! $row->permissions_label !!}
</x-livewire-tables::table.td>

<x-livewire-tables::table.td>
    @include('backend.auth.user.includes.actions', ['user' => $row])
</x-livewire-tables::table.td>
