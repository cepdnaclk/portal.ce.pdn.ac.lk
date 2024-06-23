<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ App\Domains\NewsItem\Models\NewsItem::types()[$row->type] }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->description }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->image }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->enabled ? 'Enabled' : 'Disabled' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->link_url }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->link_caption }}
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
