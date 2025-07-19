@props([
    /** The HTML content of your tooltip body (string or rendered HTML) */
    'content',
    /** URL the “Edit” link should point to */
    'editUrl',
    /** Tooltip placement: top, bottom, left, right, auto */
    'placement' => 'top',
])

<button type="button" {{ $attributes->merge(['class' => 'btn btn-sm']) }} data-bs-toggle="popover" data-bs-html="true"
    data-bs-placement="{{ $placement }}"
    data-bs-content="{!! $content !!} @if ($editUrl !== '#') <div class='mt-2 text-end'><a href='{{ $editUrl }}'>Edit</a></div> @endif">
    <i class="fa fa-info"></i> {{ __('Info') }} {{ $slot }}
</button>
