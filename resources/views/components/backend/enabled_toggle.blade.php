@if ($row->enabled)
    <i class="fas fa-toggle-on fa-2x" style="cursor: pointer;" wire:click="toggleEnable({{ $row->id }})"></i>
@else
    <i class="fas fa-toggle-on fa-2x fa-rotate-180" style="cursor: pointer;"
        wire:click="toggleEnable({{ $row->id }})"></i>
@endif
