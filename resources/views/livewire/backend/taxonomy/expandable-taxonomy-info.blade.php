<div>
    @if($taxonomy && $taxonomy->description)
        <div class="mb-3">
            <button wire:click="toggleInfo" type="button" class="btn btn-outline-secondary btn-sm">
                <i class="cil-info {{ $isExpanded ? 'icon-rotate-90' : '' }}"></i>
                {{ $isExpanded ? __('Hide Taxonomy Information') : __('Show Taxonomy Information') }}
            </button>

            @if($isExpanded)
                <div class="card mt-2">
                    <div class="card-body">
                        <h5 class="card-title">{{ $taxonomy->name }}</h5>
                        <p class="card-text">{!! $taxonomy->description !!}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
