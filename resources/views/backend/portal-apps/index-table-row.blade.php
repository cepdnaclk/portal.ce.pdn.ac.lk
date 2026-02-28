@php
    $keyCount = $row->apiKeys()->count();
    $activeKeyCount = $row->activeKeys()->count();
    $expiredKeyCount = $row->expiredKeys()->count();
@endphp

<x-livewire-tables::table.row>
    <x-livewire-tables::table.cell>
        {{ $row->name }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        <span class="badge bg-{{ $row->status === 'active' ? 'success' : 'secondary' }}">
            {{ ucfirst($row->status) }}
        </span>
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        <div>@lang('Active'): {{ $activeKeyCount ?? 0 }}</div>
        <div>@lang('Expired'): {{ $expiredKeyCount ?? 0 }}</div>
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{-- Revoke/Activate --}}
        @if ($row->status === App\Domains\Email\Models\PortalApp::STATUS_ACTIVE)
            <button class="btn btn-warning btn-sm btn-w-100" type="button"
                wire:click="toggleStatus('{{ $row->id }}')">
                @lang('Revoke')
            </button>
        @else
            <button class="btn btn-success btn-sm btn-w-100" type="button"
                wire:click="toggleStatus('{{ $row->id }}')">
                @lang('Activate')
            </button>
        @endif

        {{-- Keys --}}
        <x-utils.link :href="route('dashboard.services.apps.keys', $row)" class="btn btn-primary btn-sm  btn-w-100" icon="fa fa-key"
            text="{{ _('API Keys') }}" />

        {{-- Delete --}}
        @if ($activeKeyCount === 0)
            <x-utils.delete-button :href="route('dashboard.services.apps.destroy', $row)" :text="__('')" />
        @endif
    </x-livewire-tables::table.cell>

</x-livewire-tables::table.row>
