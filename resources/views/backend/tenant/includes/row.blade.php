@php
    $assignmentsCount = $row->users_count + $row->roles_count + $row->news_count + $row->events_count + $row->announcements_count;
@endphp

<x-livewire-tables::bs4.table.cell>
    {{ $row->name }}
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    {{ $row->slug }}
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    {{ $row->url }}
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    @if ($row->is_default)
        <span class="badge badge-success">@lang('Default')</span>
    @else
        <span class="badge badge-secondary">@lang('No')</span>
    @endif
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    <div>@lang('Users'): {{ $row->users_count }}</div>
    <div>@lang('Roles'): {{ $row->roles_count }}</div>
    <div>@lang('Content'): {{ $row->news_count + $row->events_count + $row->announcements_count }}</div>
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    @include('backend.tenant.includes.actions', ['model' => $row, 'assignmentsCount' => $assignmentsCount])
</x-livewire-tables::bs4.table.cell>
