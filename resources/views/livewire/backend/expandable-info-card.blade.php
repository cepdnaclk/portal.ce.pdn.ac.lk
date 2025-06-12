<div class="mb-2">
    <div class="d-flex justify-content-end align-items-center">
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse"
            data-bs-target="#info-card" aria-expanded="false" aria-controls="info-card">
            <i class="fa fa-info"></i>
            {{ __('Info') }}
        </button>
    </div>

    <div class="collapse mt-2 {{ $isExpanded ? 'show' : '' }}" id="info-card">
        <x-backend.card name="info-card">
            <x-slot name="header">
                {{ $title }}
            </x-slot>

            <x-slot name="body">
                <div class="card-body">
                    <p class="card-text">{!! $description !!}</p>
                </div>
            </x-slot>
        </x-backend.card>
    </div>
</div>
