<?php use App\Domains\Auth\Models\User; ?>

<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <img class="mt-1" src="{{ $row->thumbURL() }}" alt="Image" style="max-width: 200px; max-height: 200px;" />
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @php
        $desc = strip_tags($row->description);
        $desc = str_replace('&nbsp;', ' ', $desc);
    @endphp
    @if (mb_strlen($desc) > 250)
        {{ mb_substr($desc, 0, 250) }}...
    @else
        {{ $desc }}
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @include('components.backend.enabled_toggle')
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ User::find($row->created_by)->name }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->published_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ route('dashboard.news.preview', $row) }}" class="btn  btn-warning">
                <i class="fa fa-eye" title="Preview"></i>
            </a>
            <a href="{{ route('dashboard.news.edit', $row) }}" class="btn  btn-info"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.news.delete', $row) }}" class="btn  btn-danger"><i class="fa fa-trash"
                    title="Delete"></i>
            </a>

        </div>
    </div>
</x-livewire-tables::table.cell>
