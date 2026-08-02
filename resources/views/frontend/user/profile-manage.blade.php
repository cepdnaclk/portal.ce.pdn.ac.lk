@extends('frontend.layouts.app')

@section('title', __('My Profile'))

@section('content')
    @php
        use App\Domains\Profile\Models\UserProfileLink;
        use App\Domains\Profile\Models\UserProfileType;
        use App\Domains\Profile\Models\UserProfile;

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
                        @lang('Manage Profile')
                    </x-slot>

                    <x-slot name="headerActions">
                        <x-utils.link :href="route('intranet.user.account')" :text="__('Back to My Account')" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-3 text-md-right">
                                <strong>{{ __('Profile completeness') }}</strong>
                            </div>
                            <div class="col-md-9">
                                <div class="progress" aria-label="{{ __('Profile completeness') }}">
                                    <div class="progress-bar {{ $profile->isComplete() ? 'bg-success' : 'bg-warning' }}"
                                        role="progressbar" style="width: {{ $profile->completeness }}%"
                                        aria-valuenow="{{ $profile->completeness }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $profile->completeness }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-3 text-md-right">
                                <strong>{{ __('Profile Types') }}</strong>
                            </div>
                            <div class="col-md-9">
                                @forelse ($profile->profileTypes as $profileType)
                                    <span class="badge bg-info">{{ str_replace('_', ' ', $profileType->type) }}</span>
                                @empty
                                    <span class="text-muted">{{ __('None') }}</span>
                                @endforelse
                                <small class="form-text text-muted d-block">
                                    {{ __('Profile types are assigned by administrators. Contact an administrator to request changes.') }}
                                </small>
                            </div>
                        </div>

                        <x-forms.patch :action="route('intranet.user.profile.manage.update')">
                            <h5 class="border-bottom pb-2 mt-4 mb-3">{{ __('Names') }}</h5>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-md-right"
                                    for="honorific">@lang('Honorific')</label>
                                <div class="col-md-3">
                                    <select name="honorific" id="honorific" class="form-select">
                                        <option value="">{{ __('Select an honorific') }}</option>
                                        @foreach (UserProfile::HONORIFIC_OPTIONS as $value => $label)
                                            <option value="{{ $value }}" @selected(old('honorific', $profile->honorific) === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('honorific')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>

                            @foreach ([
            'full_name' => __('Full Name'),
            'name_with_initials' => __('Name with Initials'),
            'preferred_short_name' => __('Preferred Short Name'),
            'preferred_long_name' => __('Preferred Long Name'),
        ] as $field => $label)
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label text-md-right"
                                        for="{{ $field }}">{{ $label }}</label>
                                    <div class="col-md-9">
                                        <input type="text" name="{{ $field }}" id="{{ $field }}"
                                            class="form-control" value="{{ old($field, $profile->{$field}) }}" />
                                        @error($field)
                                            <strong class="text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            <h5 class="border-bottom pb-2 mt-4 mb-3">{{ __('Location & Affiliation') }}</h5>

                            @foreach ([
            'location' => __('Location'),
            'current_affiliation' => __('Current Affiliation'),
            'current_position' => __('Current Position'),
            'profile_image' => __('Profile Image URL'),
        ] as $field => $label)
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label text-md-right"
                                        for="{{ $field }}">{{ $label }}</label>
                                    <div class="col-md-9">
                                        <input type="text" name="{{ $field }}" id="{{ $field }}"
                                            class="form-control" value="{{ old($field, $profile->{$field}) }}" />
                                        @error($field)
                                            <strong class="text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            @if ($profile->profileTypes->isNotEmpty())
                                <h5 class="border-bottom pb-2 mt-4 mb-3">{{ __('Profile Details') }}</h5>

                                @foreach ($profile->profileTypes as $profileType)
                                    <div class="form-group row">
                                        <div class="col-md-3 col-form-label text-md-right">
                                            {{ ucwords(strtolower(str_replace('_', ' ', $profileType->type))) }}
                                        </div>
                                        <div class="col-md-9">
                                            <div class="border rounded p-3">
                                                <div class="row">
                                                    @if ($profileType->type === UserProfileType::TYPE_STUDENT)
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label"
                                                                for="student_reg_number">@lang('Registration Number')</label>
                                                            <input type="text" id="student_reg_number"
                                                                class="form-control"
                                                                value="{{ $typeAttr($profileType, 'reg_number') }}"
                                                                disabled />
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label"
                                                                for="student_batch">@lang('Batch')</label>
                                                            <input type="text" id="student_batch" class="form-control"
                                                                value="{{ $typeAttr($profileType, 'batch') }}" disabled />
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label"
                                                                for="student_interests">@lang('Interests')</label>
                                                            <input type="text"
                                                                name="types[STUDENT][attributes][interests]"
                                                                id="student_interests" class="form-control"
                                                                placeholder="{{ __('Comma-separated values') }}"
                                                                value="{{ $typeAttr($profileType, 'interests') }}" />
                                                            @error('types.STUDENT.attributes.interests')
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            @enderror
                                                        </div>
                                                    @elseif ($profileType->type === UserProfileType::TYPE_ACADEMIC_STAFF)
                                                        <div class="col-md-6">
                                                            <label class="form-label"
                                                                for="academic_designation">@lang('Designation')</label>
                                                            <input type="text"
                                                                name="types[ACADEMIC_STAFF][attributes][designation]"
                                                                id="academic_designation" class="form-control"
                                                                value="{{ $typeAttr($profileType, 'designation') }}" />
                                                            @error('types.ACADEMIC_STAFF.attributes.designation')
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label"
                                                                for="academic_research_interests">@lang('Research Interests')</label>
                                                            <input type="text"
                                                                name="types[ACADEMIC_STAFF][attributes][research_interests]"
                                                                id="academic_research_interests" class="form-control"
                                                                placeholder="{{ __('Comma-separated values') }}"
                                                                value="{{ $typeAttr($profileType, 'research_interests') }}" />
                                                            @error('types.ACADEMIC_STAFF.attributes.research_interests')
                                                                <strong class="text-danger">{{ $message }}</strong>
                                                            @enderror
                                                        </div>
                                                    @else
                                                        @foreach ([
            'affiliation' => __('Affiliation'),
            'position' => __('Position'),
            'interests' => __('Interests'),
        ] as $field => $label)
                                                            <div class="col-md-4">
                                                                <label class="form-label"
                                                                    for="external_{{ $field }}">{{ $label }}</label>
                                                                <input type="text"
                                                                    name="types[EXTERNAL][attributes][{{ $field }}]"
                                                                    id="external_{{ $field }}"
                                                                    class="form-control"
                                                                    @if ($field === 'interests') placeholder="{{ __('Comma-separated values') }}" @endif
                                                                    value="{{ $typeAttr($profileType, $field) }}" />
                                                                @error("types.EXTERNAL.attributes.$field")
                                                                    <strong class="text-danger">{{ $message }}</strong>
                                                                @enderror
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <h5 class="border-bottom pb-2 mt-4 mb-3">{{ __('Links') }}</h5>

                            @foreach (UserProfileLink::LINK_TYPE_LABELS as $linkType => $linkLabel)
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label text-md-right"
                                        for="links_{{ $linkType }}">{{ $linkLabel }}</label>
                                    <div class="col-md-9">
                                        <input type="url" name="links[{{ $linkType }}]"
                                            id="links_{{ $linkType }}" class="form-control"
                                            placeholder="https://example.com"
                                            value="{{ old("links.$linkType", $profileLinks->get($linkType)) }}" />
                                        @error("links.$linkType")
                                            <strong class="text-danger">{{ $message }}</strong>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            <div class="form-group row mb-0">
                                <div class="col-md-9 offset-md-3">
                                    <button class="btn btn-sm btn-primary float-end" type="submit">
                                        @lang('Update Profile')
                                    </button>
                                </div>
                            </div>
                        </x-forms.patch>
                    </x-slot>
                </x-frontend.card>
            </div>
        </div>
    </div>
@endsection
