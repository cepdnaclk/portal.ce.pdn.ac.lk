<x-livewire-tables::table.row>
    <x-livewire-tables::table.cell>
        <span class="text-monospace">{{ $row->id }}</span>
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ $row->portalApp?->name ?? '-' }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ $row->subject }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        <div>{{ implode(', ', $row->to ?? []) }}</div>
        @if (!empty($row->cc))
            <div class="text-muted small">@lang('CC'): {{ implode(', ', $row->cc) }}</div>
        @endif
        @if (!empty($row->bcc))
            <div class="text-muted small">@lang('BCC'): {{ implode(', ', $row->bcc) }}</div>
        @endif
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ ucfirst($row->status) }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ $row->sent_at?->format('Y-m-d H:i') ?? '-' }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ $row->created_at?->format('Y-m-d H:i') ?? '-' }}
    </x-livewire-tables::table.cell>
</x-livewire-tables::table.row>
