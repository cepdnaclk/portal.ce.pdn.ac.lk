<x-livewire-tables::table.cell>
    {{ $row->slug }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($row->taxonomy)
        <a href="{{ route('dashboard.taxonomy.terms.index', $row->taxonomy) }}">{{ $row->taxonomy->name }}</a>
    @else
        —
    @endif
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
        {{-- Download --}}
        <a href="{{ route('download.taxonomy-page', ['slug' => $row->slug]) }}" class="btn btn-sm btn-secondary me-3"
            target="_blank">
            <i class="fa fa-globe" title="{{ __('Web') }}"></i>
        </a>

        <!-- Manage Button -->
        <div class="btn-group" role="group" aria-label="{{ __('Actions') }}">

            {{-- View Button --}}
            <a href="{{ route('dashboard.taxonomy-pages.view', $row) }}" class="btn btn-sm btn-primary">
                <i class="fa fa-eye" title="{{ __('Preview') }}"></i>
            </a>

            @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.page.editor'))
                {{-- Edit --}}
                <a href="{{ route('dashboard.taxonomy-pages.edit', $row) }}" class="btn btn-sm btn-warning">
                    <i class="fa fa-pencil" title="{{ __('Edit') }}"></i>
                </a>

                {{-- Delete --}}
                <a href="{{ route('dashboard.taxonomy-pages.delete', $row) }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-trash" title="{{ __('Delete') }}"></i>
                </a>
            @endif
        </div>
    </div>
</x-livewire-tables::table.cell>
