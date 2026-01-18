<x-livewire-tables::table.cell>
    @if ($row->area == App\Domains\Announcement\Models\Announcement::TYPE_FRONTEND)
        Frontend
    @elseif($row->area == App\Domains\Announcement\Models\Announcement::TYPE_BACKEND)
        Backend
    @else
        Both
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @php
        $typeConfig = [
            'info' => ['class' => 'text-info', 'icon' => 'fa-info-circle'],
            'danger' => ['class' => 'text-danger', 'icon' => 'fa-exclamation-circle'],
            'warning' => ['class' => 'text-warning', 'icon' => 'fa-exclamation-triangle'],
            'success' => ['class' => 'text-success', 'icon' => 'fa-check-circle'],
        ];
        $typeLabel = App\Domains\Announcement\Models\Announcement::types()[$row->type] ?? 'Unknown';
        $typeMeta = $typeConfig[$row->type] ?? ['class' => 'text-secondary', 'icon' => 'fa-circle'];
    @endphp

    <i class="fas {{ $typeMeta['icon'] }} {{ $typeMeta['class'] }}" title="{{ $typeLabel }}"
        aria-label="{{ $typeLabel }}"></i>

</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->message }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($logged_in_user->hasAllAccess() || $logged_in_user->hasPermissionTo('user.access.editor.announcements'))
        @if ($row->enabled)
            <i class="fas fa-toggle-on fa-2x" style="color: #0ca678; cursor: pointer;"
                wire:click="toggleEnable({{ $row->id }})"></i>
        @else
            <i class="fas fa-toggle-on fa-2x fa-rotate-180" style="color: #3c4b64; cursor: pointer;"
                wire:click="toggleEnable({{ $row->id }})"></i>
        @endif
    @else
        <i class="fas fa-toggle-on fa-2x fa-rotate-180" style="color: #3c4b64; opacity: 0.6;"></i>
    @endif
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->tenant?->name ?? '-' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->starts_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->ends_at }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    @if ($logged_in_user->hasAllAccess() || $logged_in_user->hasPermissionTo('user.access.editor.announcements'))
        <div class="d-flex px-0 mt-0 mb-0">
            <div class="btn-group" role="group" aria-label="">
                <a href="{{ route('dashboard.announcements.edit', $row) }}" class="btn btn-info btn-xs"><i
                        class="fa fa-pencil" title="Edit"></i>
                </a>
                <a href="{{ route('dashboard.announcements.delete', $row) }}" class="btn btn-danger btn-xs"><i
                        class="fa fa-trash" title="Delete"></i>
                </a>
            </div>
        </div>
    @endif
</x-livewire-tables::table.cell>
