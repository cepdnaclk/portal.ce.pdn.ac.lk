<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <img class="mt-1" src="{{ $row->thumbURL() }}" alt="Image preview" style="max-width: 135px; max-height: 135px;" />
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div>
        @if (strlen($row->description) > 250)
            {{ mb_substr(strip_tags($row->description), 0, 250) }}...
        @else
            {{ strip_tags($row->description) }}
        @endif
    </div>
    <br />
    <a href="{{ $row->link_url }}" target="_blank">{{ $row->link_caption }}</a>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @include('components.backend.enabled_toggle')
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($row->start_at && $row->end_at)
        From: {{ $row->start_at }}
    @else
        On: {{ $row->start_at }}
    @endif

    @if ($row->end_at)
        <br> To: {{ $row->end_at }}
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="custom-width-1" style="width: 75px;">
        {{ $row->location }}
    </div>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ App\Domains\Auth\Models\User::find($row->created_by)->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ route('dashboard.event.preview', $row) }}" class="btn  btn-warning">
                <i class="fa fa-eye" title="Preview"></i>
            </a>
            <a href="{{ route('dashboard.event.edit', $row) }}" class="btn btn-info"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.event.delete', $row) }}" class="btn btn-danger"><i class="fa fa-trash"
                    title="Delete"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>
