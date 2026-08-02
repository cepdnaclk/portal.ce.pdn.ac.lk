<x-livewire-tables::table.cell>
    {{ $row->type_label }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->full_name ?: 'N/A' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <a href="mailto:{{ $row->email }}">{{ $row->email }}</a>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->completeness }}%
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->updated_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="{{ __('My Profile Actions') }}">
            <a href="{{ route('dashboard.my-profiles.history', $row) }}" class="btn btn-sm btn-info">
                <i class="fa fa-clock" title="{{ __('History') }}"></i>
            </a>
            <a href="{{ route('dashboard.my-profiles.edit', $row) }}" class="btn btn-sm btn-warning">
                <i class="fa fa-pencil" title="{{ __('Edit') }}"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>
