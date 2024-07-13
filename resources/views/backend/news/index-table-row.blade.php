<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>



{{-- <x-livewire-tables::table.cell>
    <div class="custom-width-2" style="width: 175px;">
        @php
        $words = explode(' ', $row->description);
        $limitedDescription = implode(' ', array_slice($words, 0, 50));
        $remainingWords = count($words) - 50;
    @endphp
    {!! $remainingWords > 0 ? $limitedDescription . '&nbsp;<a href="#" class="show-more" data-id="' . $row->id . '">Show more >>></a><span id="full-description-' . $row->id . '" style="display: none;">' . implode(' ', array_slice($words, 10)) . '</span><a href="#" class="show-less" data-id="' . $row->id . '" style="display: none;"> Show less <<<</a>' : $row->description !!}

    </div>
</x-livewire-tables::table.cell> --}}

<x-livewire-tables::table.cell>
    <img class="mt-1" src="{{ $row->image ? asset('storage/' . $row->image) : asset('Events/no-image.png') }}" alt="Image preview" style="max-width: 200px; max-height: 200px;" />
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->author }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <a href="{{ $row->link_url }}" target="_blank">{{ $row->link_caption }}</a>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->enabled ? 'Enabled' : 'Disabled' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="custom-width-1" style="width: 75px;">
        {{ $row->created_at }}
    </div>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="custom-width-1" style="width: 75px;">
        {{ $row->updated_at }}
    </div>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ route('dashboard.news.edit', $row) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.news.delete', $row) }}" class="btn btn-danger btn-xs"><i
                    class="fa fa-trash" title="Delete"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>
