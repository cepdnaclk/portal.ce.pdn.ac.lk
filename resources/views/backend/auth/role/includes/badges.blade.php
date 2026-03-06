@if ($roles->count())
    @foreach ($roles as $role)
        <a href="{{ route('dashboard.auth.role.users', $role) }}"><span
                class="badge bg-primary me-1">{{ $role->name }}</span></a>
    @endforeach
@else
    @lang('None')
@endif
