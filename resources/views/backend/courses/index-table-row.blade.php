<x-livewire-tables::table.cell>
    {{ $row->code }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->semester->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->academicProgram() }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->type }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->version() }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->credits }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->updatedUser->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->updated_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ route('dashboard.courses.edit', $row) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.courses.delete', $row) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash"
                    title="Delete"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>
