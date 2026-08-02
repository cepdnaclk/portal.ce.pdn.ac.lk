@extends('frontend.layouts.app')

@section('title', __('My Profile'))

@section('content')
    @php
        use App\Domains\Profile\Models\UserProfileLink;
        use App\Domains\Profile\Models\UserProfileType;

        $profileLinks = $profile->links->pluck('url', 'type');
        $typeAttr = function ($profileType, $key) {
            $value = old(
                "types.{$profileType->type}.attributes.$key",
                $profileType->getAttribute('attributes')[$key] ?? '',
            );
            return is_array($value) ? implode(', ', $value) : $value;
        };
    @endphp

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <x-frontend.card>
                    <x-slot name="header">
                        @lang('My Profile')
                    </x-slot>

                    <x-slot name="body">
                        <div class="mb-4">
                            <div class="progress" style="max-width: 300px;">
                                <div class="progress-bar {{ $profile->isComplete() ? 'bg-success' : 'bg-warning' }}"
                                    role="progressbar" style="width: {{ $profile->completeness }}%">
                                    {{ $profile->completeness }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ __('Profile completeness') }}</small>
                        </div>

                        <div class="mb-4">
                            <strong>{{ __('Profile Types') }}:</strong>
                            @forelse ($profile->profileTypes as $profileType)
                                <span class="badge bg-info">{{ $profileType->type }}</span>
                            @empty
                                <span class="text-muted">{{ __('None') }}</span>
                            @endforelse
                            <br>
                            <small
                                class="text-muted">{{ __('Profile types are assigned by administrators. Contact an admin to request changes.') }}</small>
                        </div>

                        <x-forms.patch :action="route('intranet.user.profile.manage.update')">
                            <h5>{{ __('Names') }}</h5>
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label" for="honorific">@lang('Honorific')</label>
                                    <input type="text" name="honorific" id="honorific" class="form-control"
                                        maxlength="20" value="{{ old('honorific', $profile->honorific) }}" />
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label" for="full_name">@lang('Full Name')</label>
                                    <input type="text" name="full_name" id="full_name" class="form-control"
                                        value="{{ old('full_name', $profile->full_name) }}" />
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label" for="name_with_initials">@lang('Name with Initials')</label>
                                    <input type="text" name="name_with_initials" id="name_with_initials"
                                        class="form-control"
                                        value="{{ old('name_with_initials', $profile->name_with_initials) }}" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="preferred_short_name">@lang('Preferred Short Name')</label>
                                    <input type="text" name="preferred_short_name" id="preferred_short_name"
                                        class="form-control"
                                        value="{{ old('preferred_short_name', $profile->preferred_short_name) }}" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="preferred_long_name">@lang('Preferred Long Name')</label>
                                    <input type="text" name="preferred_long_name" id="preferred_long_name"
                                        class="form-control"
                                        value="{{ old('preferred_long_name', $profile->preferred_long_name) }}" />
                                </div>
                            </div>

                            <h5 class="mt-3">{{ __('Location & Affiliation') }}</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="location">@lang('Location')</label>
                                    <input type="text" name="location" id="location" class="form-control"
                                        value="{{ old('location', $profile->location) }}" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="current_affiliation">@lang('Current Affiliation')</label>
                                    <input type="text" name="current_affiliation" id="current_affiliation"
                                        class="form-control"
                                        value="{{ old('current_affiliation', $profile->current_affiliation) }}" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="current_position">@lang('Current Position')</label>
                                    <input type="text" name="current_position" id="current_position" class="form-control"
                                        value="{{ old('current_position', $profile->current_position) }}" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="profile_image">@lang('Profile Image URL')</label>
                                    <input type="text" name="profile_image" id="profile_image" class="form-control"
                                        value="{{ old('profile_image', $profile->profile_image) }}" />
                                </div>
                            </div>

                            @if ($profile->profileTypes->isNotEmpty())
                                <h5 class="mt-3">{{ __('Type Details') }}</h5>
                                @foreach ($profile->profileTypes as $profileType)
                                    <div class="border rounded p-3 mb-3">
                                        <span class="badge bg-info mb-2">{{ $profileType->type }}</span>
                                        <div class="row">
                                            @if ($profileType->type === UserProfileType::TYPE_STUDENT)
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">@lang('Reg. Number')</label>
                                                    <input type="text" class="form-control" disabled
                                                        value="{{ $typeAttr($profileType, 'reg_number') }}" />
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">@lang('Batch')</label>
                                                    <input type="text" class="form-control" disabled
                                                        value="{{ $typeAttr($profileType, 'batch') }}" />
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">@lang('Interests (comma separated)')</label>
                                                    <input type="text" name="types[STUDENT][attributes][interests]"
                                                        class="form-control"
                                                        value="{{ $typeAttr($profileType, 'interests') }}" />
                                                </div>
                                            @elseif ($profileType->type === UserProfileType::TYPE_ACADEMIC_STAFF)
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">@lang('Designation')</label>
                                                    <input type="text"
                                                        name="types[ACADEMIC_STAFF][attributes][designation]"
                                                        class="form-control"
                                                        value="{{ $typeAttr($profileType, 'designation') }}" />
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">@lang('Research Interests (comma separated)')</label>
                                                    <input type="text"
                                                        name="types[ACADEMIC_STAFF][attributes][research_interests]"
                                                        class="form-control"
                                                        value="{{ $typeAttr($profileType, 'research_interests') }}" />
                                                </div>
                                            @else
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">@lang('Affiliation')</label>
                                                    <input type="text" name="types[EXTERNAL][attributes][affiliation]"
                                                        class="form-control"
                                                        value="{{ $typeAttr($profileType, 'affiliation') }}" />
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">@lang('Position')</label>
                                                    <input type="text" name="types[EXTERNAL][attributes][position]"
                                                        class="form-control"
                                                        value="{{ $typeAttr($profileType, 'position') }}" />
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">@lang('Interests (comma separated)')</label>
                                                    <input type="text" name="types[EXTERNAL][attributes][interests]"
                                                        class="form-control"
                                                        value="{{ $typeAttr($profileType, 'interests') }}" />
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <h5 class="mt-3">{{ __('Links') }}</h5>
                            <div class="row">
                                @foreach (UserProfileLink::LINK_TYPE_LABELS as $linkType => $linkLabel)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="links_{{ $linkType }}">
                                            {{ $linkLabel }}
                                        </label>
                                        <input type="text" name="links[{{ $linkType }}]"
                                            id="links_{{ $linkType }}" class="form-control" placeholder="https://…"
                                            value="{{ old("links.$linkType", $profileLinks->get($linkType)) }}" />
                                        @error("links.$linkType")
                                            <strong class="text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mb-0">
                                <div class="col col-md-12">
                                    <button class="btn btn-sm btn-primary float-end"
                                        type="submit">@lang('Update')</button>
                                </div>
                            </div>
                        </x-forms.patch>
                    </x-slot>
                </x-frontend.card>
            </div>
        </div>
    </div>
@endsection
