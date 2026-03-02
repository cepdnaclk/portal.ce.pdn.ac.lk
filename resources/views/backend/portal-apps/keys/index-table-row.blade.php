<x-livewire-tables::table.cell>
    <span class="text-monospace">{{ $row->key_prefix ?? '-' }}</span>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->created_at?->format('Y-m-d H:i') ?? '-' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->last_used_at?->format('Y-m-d H:i') ?? '-' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->expires_at?->format('Y-m-d') ?? '-' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->revoked_at?->format('Y-m-d H:i') ?? '-' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div>
        {{-- Revoke --}}
        @if (auth()->user()
                ?->hasAnyPermission(['user.access.services.apps']) || auth()->user()?->hasAllAccess())
            @if (!$row->revoked_at)
                <form method="POST" action="{{ route('dashboard.services.apps.keys.revoke', $row) }}">
                    @csrf
                    <button class="btn btn-sm btn-danger" type="submit">@lang('Revoke')</button>
                </form>
            @else
                <span class="text-muted">@lang('Revoked')</span>
            @endif
        @else
            <span class="text-muted">@lang('No access')</span>
        @endif
    </div>
</x-livewire-tables::table.cell>
