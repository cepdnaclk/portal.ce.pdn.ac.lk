@if ($tenants->count())
    @foreach ($tenants as $tenant)
        <a href="{{ route('dashboard.tenants.users', $tenant) }}"><span
                class="badge bg-secondary me-1">{{ $tenant->name }}</span></a>
    @endforeach
@else
    @lang('None')
@endif
