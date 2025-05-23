<?php use App\Domains\Auth\Models\User; ?>

<x-livewire-tables::table.cell>
    {{ $row->code }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->name }}
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
        <!-- Manage Button -->
        <a href="{{ route('dashboard.taxonomy.terms.index', $row) }}" class="btn btn-sm btn-secondary me-3">
            <i class="fa fa-list" title="Manage"></i>
        </a>

        <div class="btn-group" role="group" aria-label="">

            <!-- View Button -->
            <a href="{{ route('dashboard.taxonomy.view', $row) }}" class="btn btn-sm btn-primary">
                <i class="fa fa-eye" title="View"></i>
            </a>

            @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.data.editor'))
                <!-- Edit Button -->
                <a href="{{ route('dashboard.taxonomy.edit', $row) }}" class="btn btn-sm btn-warning">
                    <i class="fa fa-pencil" title="Edit"></i>
                </a>

                <!-- Delete Button -->
                <a href="{{ route('dashboard.taxonomy.delete', $row) }}" class="btn btn-sm btn-danger">
                    <i class="fa fa-trash" title="Delete"></i>
                </a>
            @endif



        </div>
    </div>
</x-livewire-tables::table.cell>
