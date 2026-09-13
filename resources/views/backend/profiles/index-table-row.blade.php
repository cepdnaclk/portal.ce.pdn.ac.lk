<x-livewire-tables::table.cell>
    {{ $row->email }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->name_with_initials ?? '—' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @forelse ($row->profileTypes as $profileType)
        <span class="badge bg-info">{{ str_replace('_', ' ', $profileType->type) }}</span>
    @empty
        —
    @endforelse
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($row->user)
        <span class="badge bg-success">{{ __('Linked') }}</span>
    @else
        <span class="badge bg-secondary">{{ __('None') }}</span>
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->completeness }}%
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ optional($row->updated_at)->format('Y-m-d H:i') }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group me-3" role="group" aria-label="{{ __('View profile') }}">
            <a href="{{ route('dashboard.profiles.show', $row) }}" class="btn btn-sm btn-primary">
                <i class="fa fa-eye" title="{{ __('View') }}"></i>
            </a>
        </div>

        @if ($logged_in_user->hasPermissionTo('user.access.profiles.editor'))
            <div class="btn-group" role="group" aria-label="{{ __('Manage profile') }}">
                <a href="{{ route('dashboard.profiles.edit', $row) }}" class="btn btn-sm btn-warning">
                    <i class="fa fa-pencil" title="{{ __('Edit') }}"></i>
                </a>

                <a href="{{ route('dashboard.profiles.delete', $row) }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-trash" title="{{ __('Delete') }}"></i>
                </a>
            </div>
        @endif
    </div>
</x-livewire-tables::table.cell>
