<div class="btn-group" role="group" aria-label="{{ __('Actions') }}">
    {{-- Edit --}}
    <a href="{{ route('dashboard.tenants.edit', $model) }}" class="btn btn-sm btn-warning">
        <i class="fa fa-pencil" title="{{ __('Edit') }}"></i>
    </a>

    @if ($assignmentsCount === 0)
        {{-- Delete --}}
        <a href="{{ route('dashboard.tenants.destroy', $model) }}" class="btn btn-sm btn-danger">
            <i class="fa fa-trash" title="{{ __('Delete') }}"></i>
        </a>
    @endif
</div>
