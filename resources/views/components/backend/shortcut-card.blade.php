@props([
    // the target URL
    'route',
    // color = ['primary', 'info','success', 'danger','warning','secondary' ]
    'color' => 'primary',
    // label need to display with the shortcut
    'label',
    // fa icon, default is first 2 chars of the label
    'icon' => null,
])

<div class="col-4 col-sm-3 col-md-2 col-lg-2" style="max-width: 10rem;">
    <a href="{{ $route }}"
        class="d-flex flex-column align-items-center justify-content-start text-center border rounded bg-white pt-2 p-1 shadow-sm text-decoration-none h-100 overflow-hidden">
        <span
            class="d-inline-flex align-items-center text-uppercase justify-content-center rounded-circle border border-{{ $color }} text-{{ $color }} fw-bold"
            style="width:64px;height:64px;">
            @if (!empty($icon))
                <i class="fa fa-2x {{ $icon }}"></i>
            @else
                {{ Str::substr($label, 0, 2) }}
            @endif
        </span>
        <div class="small fw-semibold p-2 text-body">
            <span class="d-inline-block">{{ $label }}</span>
        </div>
    </a>
</div>
