{{-- Tenants --}}
<div class="row pb-3">
    <div class="col-md-6">
        <h5 class="mb-3">@lang('Tenants')</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>@lang('Tenant Name')</th>
                        <th>@lang('Site')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logged_in_user->tenants as $tenant)
                        <tr>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->url }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">@lang('No tenants assigned')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Roles --}}
<div class="row pb-3">
    <div class="col-md-6">
        <h5 class="mb-3">@lang('Roles')</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>@lang('Role Name')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logged_in_user->roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center">@lang('No roles assigned')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Permissions --}}
<div class="row pb-3">
    <div class="col-md-6">
        <h5 class="mb-3">@lang('Permissions')</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th>@lang('Permission Name')</th>
                        <th>@lang('Description')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logged_in_user->getAllPermissions() as $permission)
                        <tr>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">@lang('No permissions assigned')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
