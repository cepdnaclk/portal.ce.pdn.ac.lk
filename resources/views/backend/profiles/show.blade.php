@extends('backend.layouts.app')

@section('title', __('View Profile'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            Profile : {{ $profile->email }}
        </x-slot>

        <x-slot name="headerActions">
            @if ($logged_in_user->hasPermissionTo('user.access.profiles.editor'))
                <x-utils.link icon="c-icon cil-pencil" class="card-header-action" :href="route('dashboard.profiles.edit', $profile)" :text="__('Edit')" />
            @endif
        </x-slot>

        <x-slot name="body">
            @if (session('Success'))
                <x-utils.alert type="success" dismissable="true">{{ session('Success') }}</x-utils.alert>
            @endif

            <div class="mb-4">
                <div class="progress" style="max-width: 300px;">
                    <div class="progress-bar {{ $profile->isComplete() ? 'bg-success' : 'bg-warning' }}" role="progressbar"
                        style="width: {{ $profile->completeness }}%">
                        {{ $profile->completeness }}%
                    </div>
                </div>
                <small class="text-muted">{{ __('Profile completeness') }}</small>
            </div>

            <table class="table table-sm">
                <tr>
                    <th style="width: 220px;">{{ __('Profile Picture') }}</th>
                    <td>
                        @if ($profile->profileImageUrl())
                            <img src="{{ $profile->profileImageUrl() }}" class="img-thumbnail" style="max-height: 150px;"
                                alt="{{ __('Profile picture') }}" />
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{ __('Email') }}</th>
                    <td>{{ $profile->email }}</td>
                </tr>
                <tr>
                    <th>{{ __('Alternate Email') }}</th>
                    <td>{{ $profile->alternate_email ?? '—' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Full Name') }}</th>
                    <td>{{ trim(($profile->honorific ? $profile->honorific . ' ' : '') . ($profile->full_name ?? '—')) }}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('Name with Initials') }}</th>
                    <td>{{ $profile->name_with_initials ?? '—' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Preferred Names') }}</th>
                    <td>{{ $profile->preferred_short_name ?? '—' }} / {{ $profile->preferred_long_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Location') }}</th>
                    <td>{{ $profile->location ?? '—' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Current Affiliation') }}</th>
                    <td>{{ $profile->current_affiliation ?? '—' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Current Position') }}</th>
                    <td>{{ $profile->current_position ?? '—' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Interests') }}</th>
                    <td>{{ $profile->interests ? implode(', ', $profile->interests) : '—' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Linked Account') }}</th>
                    <td>
                        @if ($profile->user)
                            {{ $profile->user->name }} ({{ $profile->user->email }})
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </table>

            <h5 class="mt-4">{{ __('Profile Types') }}</h5>
            @forelse ($profile->profileTypes as $profileType)
                <div class="border rounded p-3 mb-2">
                    <span class="badge bg-info">{{ $profileType->type }}</span>
                    <dl class="row mb-0 mt-2">
                        @foreach ($profileType->getAttribute('attributes') ?? [] as $key => $value)
                            <dt class="col-sm-3">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                            <dd class="col-sm-9">{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                        @endforeach
                    </dl>
                </div>
            @empty
                <p class="text-muted">{{ __('No profile types assigned.') }}</p>
            @endforelse

            <h5 class="mt-4">{{ __('Links') }}</h5>
            @forelse ($profile->links as $link)
                <div>
                    <strong>{{ \App\Domains\Profile\Models\UserProfileLink::LINK_TYPE_LABELS[$link->type] ?? $link->type }}:</strong>
                    <a href="{{ $link->url }}" target="_blank" rel="noopener">{{ $link->url }}</a>
                </div>
            @empty
                <p class="text-muted">{{ __('No links.') }}</p>
            @endforelse
        </x-slot>
    </x-backend.card>
@endsection
