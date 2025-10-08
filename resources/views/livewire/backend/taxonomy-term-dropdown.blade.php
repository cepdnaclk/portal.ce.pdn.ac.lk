<div class="d-flex align-items-center gap-2">
    <div class="flex-grow-1">
        @include('livewire.backend.searchable-dropdown', [
            'name' => $name,
            'options' => $options,
            'selected' => $selected,
            'placeholder' => $placeholder,
            'icon' => $icon ?? 'fa fa-globe',
            'inputId' => $inputId,
        ])

    </div>
    <a href="{{ $editUrl }}" @if ($editUrl != '#') target="_blank" @endif
        class="btn btn-outline-secondary btn-sm {{ $editUrl == '#' ? 'disabled' : '' }}" title="View taxonomy term"
        @if ($editUrl == '#') aria-disabled="true" tabindex="-1" @endif>
        <i class="fa fa-eye"></i>
    </a>
</div>
