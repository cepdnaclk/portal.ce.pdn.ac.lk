<div class="w-100 position-relative">
    <input type="hidden" name="{{ $name }}" value="{{ $selected }}"
        @if ($inputId) id="{{ $inputId }}" @endif>

    <div class="input-group" wire:key="sd-{{ md5($name) }}">
        @if ($icon)
            <span class="input-group-text"><i class="{{ $icon }}"></i></span>
        @endif

        <button type="button" class="form-control text-start d-flex justify-content-between align-items-center"
            wire:click="toggle">
            <span class="text-truncate {{ $selected ? '' : 'text-muted' }}" title="{{ $this->selectedLabel }}">
                {{ $this->selectedLabel }}
            </span>
            <span class="ms-2 d-flex align-items-center">
                @if ($selected)
                    <i class="fa fa-times text-secondary me-2" title="Clear" wire:click.stop="clear"></i>
                @endif
                <i class="fa fa-caret-down"></i>
            </span>
        </button>
    </div>

    @if ($open)
        <div class="card position-absolute w-100 mt-1 shadow" style="z-index: 1050;">
            <div class="card-body p-2">
                <input type="text" class="form-control mb-2" placeholder="Search..."
                    wire:model.debounce.200ms="search">
                <ul class="list-group overflow-auto" style="max-height: 240px;">
                    @forelse ($this->filteredOptions as $key => $label)
                        <li class="list-group-item list-group-item-action py-2 px-2" role="button"
                            wire:click="select('{{ addslashes((string) $key) }}')">
                            <small>{{ $label }}</small>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No matches</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif
</div>
