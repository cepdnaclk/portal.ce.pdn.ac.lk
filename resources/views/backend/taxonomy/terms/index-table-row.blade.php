<?php use App\Domains\Auth\Models\User; ?>

<x-livewire-tables::table.cell>
    {{ $row->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->code }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($row->parent_id != null)
        {{ $row->parent->name }}
    @else
        N/A
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
        <div class="btn-group" role="group" aria-label="">
            <!-- Edit Button -->
            <a href="{{ route('dashboard.taxonomy.terms.edit', ['taxonomy' => $row->taxonomy_id, 'term' => $row->id]) }}"
                class="btn btn-sm btn-warning">
                <i class="fa fa-pencil" title="Edit"></i>
            </a>

            <!-- Delete Button -->
            <a href="{{ route('dashboard.taxonomy.terms.delete', ['taxonomy' => $row->taxonomy_id, 'term' => $row->id]) }}"
                class="btn btn-sm btn-danger">
                <i class="fa fa-trash" title="Delete"></i>
            </a>

        </div>
    </div>
</x-livewire-tables::table.cell>
