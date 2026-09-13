@extends('backend.layouts.app')

@section('title', __('Review Profile Merge'))

@section('content')
    @php
        $profileA = $profiles[0];
        $profileB = $profiles[1];
        $rows = [
            __('Email') => 'email',
            __('Alternate Email') => 'alternate_email',
            __('Full Name') => 'full_name',
            __('Name with Initials') => 'name_with_initials',
            __('Preferred Short Name') => 'preferred_short_name',
            __('Preferred Long Name') => 'preferred_long_name',
            __('Honorific') => 'honorific',
            __('Location') => 'location',
            __('Current Affiliation') => 'current_affiliation',
            __('Current Position') => 'current_position',
        ];
    @endphp

    <x-backend.card>
        <x-slot name="header">
            {{ __('Review Profile Merge') }}
        </x-slot>

        <x-slot name="body">
            <x-utils.alert type="warning" :dismissable="false">
                {{ __('The primary profile will be kept. Its populated values win conflicts, while its empty fields are filled from the other profile. Profile types and links are consolidated.') }}
            </x-utils.alert>

            @if ($hasAccountConflict)
                <x-utils.alert type="danger" :dismissable="false">
                    {{ __('These profiles belong to different portal accounts and cannot be merged. Resolve the account links first.') }}
                </x-utils.alert>
            @endif

            {!! Form::open(['url' => route('dashboard.profiles.merge.store'), 'method' => 'post']) !!}
            @foreach ($profiles as $profile)
                <input type="hidden" name="profile_ids[]" value="{{ $profile->id }}" />
            @endforeach

            <div class="row mb-4">
                @foreach ($profiles as $index => $profile)
                    <div class="col-md-6 mb-2">
                        <label class="border rounded p-3 px-4 w-100 h-100 d-flex align-items-start">
                            <input type="radio" name="primary_profile_id" value="{{ $profile->id }}"
                                class="form-check-input me-3" {{ $index === 0 ? 'checked' : '' }}
                                {{ $hasAccountConflict ? 'disabled' : '' }} />
                            <span>
                                <strong>{{ __('Keep this profile') }}</strong><br />
                                {{ $profile->email }}<br />
                                <small
                                    class="text-muted">{{ $profile->name_with_initials ?? ($profile->full_name ?? __('No name')) }}</small>
                            </span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 22%">{{ __('Field') }}</th>
                            <th>{{ $profileA->email }}</th>
                            <th>{{ $profileB->email }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ __('Profile Picture') }}</th>
                            @foreach ($profiles as $profile)
                                <td>
                                    @if ($profile->profileImageUrl())
                                        <img src="{{ $profile->profileImageUrl() }}" class="img-thumbnail"
                                            style="max-height: 90px;" alt="{{ __('Profile picture') }}" />
                                    @else
                                        —
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @foreach ($rows as $label => $field)
                            <tr>
                                <th>{{ $label }}</th>
                                <td>{{ $profileA->{$field} ?: '—' }}</td>
                                <td>{{ $profileB->{$field} ?: '—' }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>{{ __('Linked Account') }}</th>
                            @foreach ($profiles as $profile)
                                <td>{{ $profile->user ? $profile->user->name . ' (' . $profile->user->email . ')' : '—' }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>{{ __('Profile Types') }}</th>
                            @foreach ($profiles as $profile)
                                <td>
                                    @forelse ($profile->profileTypes as $profileType)
                                        <span class="badge bg-info">{{ str_replace('_', ' ', $profileType->type) }}</span>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>{{ __('Links') }}</th>
                            @foreach ($profiles as $profile)
                                <td>
                                    @forelse ($profile->links as $link)
                                        <div>
                                            {{ \App\Domains\Profile\Models\UserProfileLink::LINK_TYPE_LABELS[$link->type] ?? $link->type }}
                                        </div>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('dashboard.profiles.merge.select') }}"
                    class="btn btn-light me-2">{{ __('Back') }}</a>
                <button type="submit" class="btn btn-danger" {{ $hasAccountConflict ? 'disabled' : '' }}
                    onclick="return confirm('{{ __('Merge these profiles? The secondary profile will be archived.') }}')">
                    {{ __('Merge Profiles') }}
                </button>
            </div>
            {!! Form::close() !!}
        </x-slot>
    </x-backend.card>
@endsection
