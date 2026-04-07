{{-- User --}}
<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered mb-0">
        <tr>
            <th>@lang('Type')</th>
            <td>@include('backend.auth.user.includes.type', ['user' => $logged_in_user])</td>
        </tr>

        <tr>
            <th>@lang('Avatar')</th>
            <td><img src="{{ $logged_in_user->avatar }}" class="user-profile-image" /></td>
        </tr>

        <tr>
            <th>@lang('Name')</th>
            <td>{{ $logged_in_user->name }}</td>
        </tr>

        <tr>
            <th>@lang('E-mail Address')</th>
            <td>{{ $logged_in_user->email }}</td>
        </tr>

        @if ($logged_in_user->isSocial())
            <tr>
                <th>@lang('Social Provider')</th>
                <td>{{ ucfirst($logged_in_user->provider) }}</td>
            </tr>
        @endif

        <tr>
            <th>@lang('Timezone')</th>
            <td>{{ $logged_in_user->timezone ? str_replace('_', ' ', $logged_in_user->timezone) : __('N/A') }}</td>
        </tr>

        <tr>
            <th>@lang('Account Created')</th>
            <td>@displayDate($logged_in_user->created_at) ({{ $logged_in_user->created_at->diffForHumans() }})</td>
        </tr>

        <tr>
            <th>@lang('Last Updated')</th>
            <td>@displayDate($logged_in_user->updated_at) ({{ $logged_in_user->updated_at->diffForHumans() }})</td>
        </tr>
    </table>
</div>

<hr />

{{-- Profiles  --}}
<div class="mt-4">
    <h5>@lang('Linked Profiles')</h5>

    @if ($profiles->isEmpty())
        <div class="alert alert-warning mb-0">
            @lang('No linked profiles exist yet.')
            <a href="{{ route('dashboard.my-profiles.index') }}" class="alert-link">@lang('Create a profile')</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>@lang('Type')</th>
                        <th>@lang('Name')</th>
                        <th>@lang('E-mail')</th>
                        <th>@lang('Completeness')</th>
                        <th>@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($profiles as $profile)
                        <tr>
                            <td>{{ $profile->type_label }}</td>
                            <td>{{ $profile->preferred_long_name ?: 'N/A' }}</td>
                            <td>{{ $profile->email }}</td>
                            <td>{{ $profileCompleteness[$profile->id] ?? $profile->completeness }}%</td>
                            <td>
                                <a href="{{ route('dashboard.my-profiles.edit', $profile) }}"
                                    class="btn btn-sm btn-primary">@lang('Edit')</a>
                                <a href="{{ route('dashboard.my-profiles.history', $profile) }}"
                                    class="btn btn-sm btn-info">@lang('History')</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
