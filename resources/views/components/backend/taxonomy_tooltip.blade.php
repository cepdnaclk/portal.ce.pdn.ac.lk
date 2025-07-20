@props([
    /** The HTML content of your tooltip body (string or rendered HTML) */
    'content',
    /** URL the “Edit” link should point to */
    'editUrl',
    /** Tooltip placement: top, bottom, left, right, auto */
    'placement' => 'top',
])

<button type="button"
    {{ $attributes->merge(['class' => 'btn btn-link text-muted text-decoration-none btn-sm rounded-pill']) }}
    data-bs-toggle="popover" data-bs-html="true" data-bs-placement="{{ $placement }}" data-bs-trigger="click"
    data-bs-content="{!! $content ?? 'The list is managed by the taxonomy module.' !!}
    @if ($editUrl !== '#') <div class='mt-2 text-end'><a target='_blank' href='{{ $editUrl }}'>Edit</a></div> @endif">
    <i class="fa fa-lg fa-info-circle mx-1"></i>{{ $slot }}
</button>
