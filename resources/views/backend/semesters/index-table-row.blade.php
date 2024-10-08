<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ \App\Domains\AcademicProgram\Semester\Models\Semester::getVersions()[$row->version] ?? 'Unknown Version' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->academicProgram() }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->description }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <a href="https://www.ce.pdn.ac.lk/academics/{{ strtolower($row->academic_program) }}/semesters/{{ $row->url }}"
        target="_blank">/{{ $row->url }}</a>
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
            <a href="{{ route('dashboard.semesters.edit', $row) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.semesters.delete', $row) }}" class="btn btn-danger btn-xs"><i
                    class="fa fa-trash" title="Delete"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>
