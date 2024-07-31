<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <img class="mt-1" src="{{ $row->thumbURL() }}" alt="Image preview" style="max-width: 135px; max-height: 135px;" />
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <a href="{{ $row->link_url }}" target="_blank">{{ $row->link_caption }}</a>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @include('components.backend.enabled_toggle')
</x-livewire-tables::table.cell>


<x-livewire-tables::table.cell>
    {{ $row->start_at }}
    @if ($row->end_at)
        - {{ $row->end_at }}
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="custom-width-1" style="width: 75px;">
        {{ $row->location }}
    </div>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ App\Domains\Auth\Models\User::find($row->user_id)->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ route('dashboard.event.edit', $row) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.event.delete', $row) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash"
                    title="Delete"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>
