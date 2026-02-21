<?php use App\Domains\Auth\Models\User; ?>

<x-livewire-tables::table.cell>
    {{ $row->file_name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($row->taxonomy)
        <a href="{{ route('dashboard.taxonomy.terms.index', $row->taxonomy) }}">
            {{ $row->taxonomy->name }}
        </a>
    @else
        —
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->tenant?->name ?? 'N/A' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->user_created->name ?? 'N/A' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->user_updated->name ?? 'N/A' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->created_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->updated_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group me-3" role="group" aria-label="View Buttons">
            {{-- View Button --}}
            <a href="{{ route('dashboard.taxonomy-files.view', $row) }}" class="btn btn-sm btn-primary">
                <i class="fa fa-eye" title="{{ __('View') }}"></i>
            </a>
            {{-- Download --}}
            <a href="{{ route('download.taxonomy-file', [
                'file_name' => $row->file_name,
                'extension' => $row->getFileExtension(),
            ]) }}"
                class="btn btn-sm btn-secondary" target="_blank">
                <i class="fa fa-download" title="{{ __('Download') }}"></i>
            </a>
        </div>

        <!-- Manage Button -->
        <div class="btn-group" role="group" aria-label="{{ __('Actions') }}">
            @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.file.editor'))
                {{-- Edit --}}
                <a href="{{ route('dashboard.taxonomy-files.edit', $row) }}" class="btn btn-sm btn-warning">
                    <i class="fa fa-pencil" title="{{ __('Edit') }}"></i>
                </a>

                {{-- Delete --}}
                <a href="{{ route('dashboard.taxonomy-files.delete', $row) }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-trash" title="{{ __('Delete') }}"></i>
                </a>
            @endif
        </div>
    </div>
</x-livewire-tables::table.cell>
