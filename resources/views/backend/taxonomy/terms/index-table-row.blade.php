<?php use App\Domains\Auth\Models\User; ?>

<x-livewire-tables::table.cell>
    {{ $row->code }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ User::find($row->created_by)->name ?? 'N/A' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ User::find($row->updated_by)->name ?? 'N/A' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <!-- Edit Button -->
            <a href="{{ route('taxonomy.terms.edit', ['taxonomy' => $taxonomy->id, 'term' => $row->id]) }}" class="btn btn-sm btn-warning">
                <i class="fa fa-pencil" title="Edit"></i>
            </a>

            <!-- Delete Button -->
            <a href="{{ route('taxonomy.terms.delete', ['taxonomy' => $taxonomy->id, 'term' => $row->id]) }}" class="btn btn-sm btn-danger">
                <i class="fa fa-trash" title="Delete"></i>
            </a>

        </div>
    </div>
</x-livewire-tables::table.cell>
