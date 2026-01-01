@if($announcement->enabled)
<i class="fas fa-toggle-on fa-2x" style="color: #0ca678; cursor: pointer;"
  wire:click="toggleEnable({{ $announcement->id }})"></i>
@else
<i class="fas fa-toggle-on fa-2x fa-rotate-180" style="color: #3c4b64; cursor: pointer;"
  wire:click="toggleEnable({{ $announcement->id }})"></i>
@endif