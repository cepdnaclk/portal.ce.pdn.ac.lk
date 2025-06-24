<x-livewire-tables::table.cell>
    {{ $row->slug }}
</x-livewire-tables::table.cell>
<x-livewire-tables::table.cell>
    @if($row->taxonomy)
        <a href="{{ route('dashboard.taxonomy.terms.index', $row->taxonomy) }}">{{ $row->taxonomy->name }}</a>
    @else
        —
    @endif
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
    <div class="btn-group" role="group">
        <a href="{{ url('/taxonomy/' . $row->slug) }}" class="btn btn-sm btn-primary" target="_blank">
            <i class="fa fa-eye"></i>
        </a>
        @if($logged_in_user->hasPermissionTo('user.taxonomy.data.editor'))
            <a href="{{ route('dashboard.taxonomy-pages.edit', $row) }}" class="btn btn-sm btn-warning">
                <i class="fa fa-pencil"></i>
            </a>
        @endif
    </div>
</x-livewire-tables::table.cell>
