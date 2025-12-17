<div class="row">
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
