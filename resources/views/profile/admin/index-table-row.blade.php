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
    @if ($row->user)
        @if (auth()->user()?->can('admin.access.user.list') || auth()->user()?->hasAllAccess())
            <a href="{{ route('dashboard.auth.user.show', $row->user) }}">{{ $row->user->name }}</a>
        @else
            {{ $row->user->name }}
        @endif
    @else
        {{ __('-') }}
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->completeness }}%
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->updated_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="{{ __('Profile Actions') }}">
            @can('user.access.profiles.view')
                <a href="{{ route('dashboard.profiles.history', $row) }}" class="btn btn-sm btn-info">
                    <i class="fa fa-clock" title="{{ __('History') }}"></i>
                </a>
            @endcan

            @can('user.access.profiles.edit')
                <a href="{{ route('dashboard.profiles.edit', $row) }}" class="btn btn-sm btn-warning">
                    <i class="fa fa-pencil" title="{{ __('Edit') }}"></i>
                </a>
            @endcan


            @can('user.access.profiles.delete')
                <a href="{{ route('dashboard.profiles.delete', $row) }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-trash" title="{{ __('Delete') }}"></i>
                </a>
            @endcan
        </div>
    </div>
</x-livewire-tables::table.cell>
