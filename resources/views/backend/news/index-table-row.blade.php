<?php use App\Domains\Auth\Models\User; ?>

<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <img class="mt-1" src="{{ $row->thumbURL() }}" alt="Image" style="max-width: 200px; max-height: 200px;" />
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if (strlen($row->description) > 250)
        {{ mb_substr(strip_tags($row->description), 0, 250) }}...
    @else
        {{ strip_tags($row->description) }}
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($row->enabled)
        <i class="fas fa-toggle-on fa-2x" style="color: #0ca678; cursor: pointer;"
            wire:click="toggleEnable({{ $row->id }})"></i>
    @else
        <i class="fas fa-toggle-on fa-2x fa-rotate-180" style="color: #3c4b64; cursor: pointer;"
            wire:click="toggleEnable({{ $row->id }})"></i>
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ User::find($row->user_id)->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->published_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ route('dashboard.news.edit', $row) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.news.delete', $row) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash"
                    title="Delete"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>
