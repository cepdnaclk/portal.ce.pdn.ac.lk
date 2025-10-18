<x-livewire-tables::table.row>
    <x-livewire-tables::table.cell>
        {{ $row->name }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ $row->taxonomy?->name ?? '—' }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ $row::DATA_TYPE_LABELS[$row->data_type] ?? ucfirst($row->data_type) }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ is_array($row->items) ? count($row->items) : 0 }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ optional($row->created_at)->format('Y-m-d H:i') }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        {{ optional($row->updated_at)->format('Y-m-d H:i') }}
    </x-livewire-tables::table.cell>

    <x-livewire-tables::table.cell>
        <div class="d-flex px-0 mt-0 mb-0">
            <div class="btn-group me-3" role="group" aria-label="{{ __('Actions') }}">
                {{-- History Button --}}
                <a href="{{ route('dashboard.taxonomy-lists.history', $row) }}" class="btn btn-sm btn-info">
                    <i class="fa fa-clock" title="{{ __('History') }}"></i>
                </a>

                {{-- View Button --}}
                <a href="{{ route('dashboard.taxonomy-lists.view', $row) }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-eye" title="{{ __('Preview') }}"></i>
                </a>
            </div>

            <!-- Manage Button -->
            <div class="btn-group" role="group" aria-label="{{ __('Actions') }}">
                @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.list.editor'))
                    {{-- Manage List --}}
                    <a href="{{ route('dashboard.taxonomy-lists.manage', $row) }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-list" title="{{ __('Manage') }}"></i>
                    </a>

                    {{-- Edit --}}
                    <a href="{{ route('dashboard.taxonomy-lists.edit', $row) }}" class="btn btn-sm btn-warning">
                        <i class="fa fa-pencil" title="{{ __('Edit') }}"></i>
                    </a>

                    {{-- Delete --}}
                    <a href="{{ route('dashboard.taxonomy-lists.delete', $row) }}" class="btn btn-sm btn-danger">
                        <i class="fa fa-trash" title="{{ __('Delete') }}"></i>
                    </a>
                @endif
            </div>
        </div>
    </x-livewire-tables::table.cell>
</x-livewire-tables::table.row>
