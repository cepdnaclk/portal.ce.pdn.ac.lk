@php
    $profile = $profile ?? new \App\Domains\Profiles\Models\Profile();
    $sharedFields = \App\Domains\Profiles\Models\Profile::syncedIdentityFields();
    $departmentOptions = config('profiles.department', []);
    $defaultDepartment = $departmentOptions[0] ?? '';
@endphp

@if ($selfService)
    <div class="alert alert-info">
        @lang('Applicable Personal Information fields and Social Profile URLs are auto-filled from your other profiles and stay synchronized across all linked profiles.')
    </div>
@endif

{{-- Profile Type --}}
<div class="form-group row">
    <label for="type" class="col-md-2 col-form-label">@lang('Profile Type')*</label>
    <div class="col-md-10">
        <select name="type" id="type" class="form-control" {{ $selfService ? 'disabled' : '' }} required>
            @foreach ($typeOptions as $value => $label)
                <option value="{{ $value }}" {{ old('type', $profile->type) === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @if ($selfService)
            <input type="hidden" name="type" value="{{ old('type', $profile->type) }}">
        @endif
    </div>
</div>

{{-- Linked User --}}
@if (!$selfService)
    <div class="form-group row">
        <label for="user_id" class="col-md-2 col-form-label">@lang('Linked User')</label>
        <div class="col-md-10">
            <select name="user_id" id="user_id" class="form-control">
                <option value="">@lang('[ Independent Profile ]')</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                        {{ (string) old('user_id', $profile->user_id) === (string) $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endif

<hr />

{{-- Personal Information --}}
<h5 class="mt-4">@lang('Personal Information')</h5>
<div class="form-group row">
    {{-- Full Name --}}
    <label for="full_name" class="col-md-2 col-form-label">@lang('Full Name')*</label>
    <div class="col-md-10">
        <input type="text" name="full_name" id="full_name" class="form-control"
            value="{{ old('full_name', $profile->full_name) }}" maxlength="255" required>
    </div>
</div>
<div class="form-group row">
    {{-- Name with initials --}}
    <label for="name_with_initials" class="col-md-2 col-form-label">@lang('Name With Initials')*</label>
    <div class="col-md-10">
        <input type="text" name="name_with_initials" id="name_with_initials" class="form-control"
            value="{{ old('name_with_initials', $profile->name_with_initials) }}" maxlength="255" required>
    </div>
</div>
<div class="form-group row">
    {{-- Preferred Short Name --}}
    <label for="preferred_short_name" class="col-md-2 col-form-label">@lang('Preferred Short Name')*</label>
    <div class="col-md-10">
        <input type="text" name="preferred_short_name" id="preferred_short_name" class="form-control"
            value="{{ old('preferred_short_name', $profile->preferred_short_name) }}" maxlength="255" required>
        <small class="form-text text-muted">@lang('The name (short) preferred to be called')</small>
    </div>
</div>
<div class="form-group row">
    {{-- Preferred Short Name --}}
    <label for="preferred_long_name" class="col-md-2 col-form-label">@lang('Preferred Long Name')*</label>
    <div class="col-md-10">
        <input type="text" name="preferred_long_name" id="preferred_long_name" class="form-control"
            value="{{ old('preferred_long_name', $profile->preferred_long_name) }}" maxlength="255" required>
        <small class="form-text text-muted">@lang('This will be used as display name')</small>
    </div>
</div>
<div class="form-group row">
    {{-- Gender --}}
    <label for="gender" class="col-md-2 col-form-label">@lang('Gender')</label>
    <div class="col-md-4">
        <select name="gender" id="gender" class="form-control">
            <option value="">@lang('Select Gender')</option>
            @foreach (\App\Domains\Profiles\Models\Profile::GENDERS as $gender)
                <option value="{{ $gender }}"
                    {{ old('gender', $profile->gender) === $gender ? 'selected' : '' }}>
                    {{ $gender }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row">
    {{-- Civil Status --}}
    <label for="civil_status" class="col-md-2 col-form-label">@lang('Civil Status')</label>
    <div class="col-md-4">
        <select name="civil_status" id="civil_status" class="form-control">
            @foreach (\App\Domains\Profiles\Models\Profile::CIVIL_STATUSES as $civilStatus)
                <option value="{{ $civilStatus }}"
                    {{ old('civil_status', $profile->civil_status) === $civilStatus ? 'selected' : '' }}>
                    {{ $civilStatus ?: __('None') }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row">
    {{-- Honorific --}}
    <label for="honorific" class="col-md-2 col-form-label">@lang('Honorific')</label>
    <div class="col-md-4">
        <select name="honorific" id="honorific" class="form-control">
            @foreach (\App\Domains\Profiles\Models\Profile::HONORIFICS as $honorific)
                <option value="{{ $honorific }}"
                    {{ old('honorific', $profile->honorific) === $honorific ? 'selected' : '' }}>
                    {{ $honorific ?: __('None') }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<hr />

{{-- Additional Personal Details --}}
<h5 class="mt-4">@lang('Additional Personal Details')</h5>

<div class="form-group row">
    {{-- Biography --}}
    <label for="biography" class="col-md-2 col-form-label">@lang('Biography')</label>
    <div class="col-md-10">
        <textarea name="biography" id="biography" class="form-control" rows="6">{{ old('biography', $profile->biography) }}</textarea>
        <small class="form-text text-muted">@lang('A brief biography or description about yourself, applicable only for Student ans Staff profiles')</small>
    </div>
</div>

<div class="form-group row">
    {{-- Profile Picture --}}
    <label for="profile_picture" class="col-md-2 col-form-label">@lang('Profile Picture')</label>
    <div class="col-md-6">
        <input type="file" name="profile_picture" id="profile_picture" class="form-control"
            accept=".jpg,.jpeg,image/jpeg">
        <small class="form-text text-muted">@lang('Only JPG/JPEG files up to 2 MB are allowed.')</small>
        @if ($profile->profile_picture)
            <div class="form-check mt-2">
                <input type="checkbox" name="remove_profile_picture" id="remove_profile_picture" value="1"
                    class="form-check-input">
                <label for="remove_profile_picture" class="form-check-label">@lang('Remove existing profile picture')</label>
            </div>
        @endif
    </div>
    @if ($profile->profile_picture)
        <div class="col-md-2">
            <div class="mt-2">
                <img src="{{ $profile->profile_picture_url }}" alt="@lang('Current profile picture')" class="img-thumbnail"
                    style="max-width: 120px; max-height: 120px;">
            </div>
        </div>
    @endif
</div>

<div class="form-group row">
    {{-- Profile CV --}}
    <label for="profile_cv" class="col-md-2 col-form-label">@lang('CV')</label>
    <div class="col-md-10">
        <input type="url" name="profile_cv" id="profile_cv" class="form-control"
            value="{{ old('profile_cv', data_get($profile, 'profile_cv')) }}" maxlength="255">
        <small class="form-text text-muted">@lang('A URL link to your CV or resume, applicable only for Student ans Staff profiles')</small>
    </div>
</div>
<hr />

{{-- Student Details --}}
<h5 class="mt-4">@lang('Student Details') <small class="h6">(@lang('Applicable only for Undergraduate Student Profiles'))</small></h5>

<div class="form-group row">
    <label for="reg_no" class="col-md-2 col-form-label">@lang('Registration Number')</label>
    <div class="col-md-4">
        <input type="text" name="reg_no" id="reg_no" class="form-control"
            value="{{ old('reg_no', $profile->reg_no) }}" maxlength="10" placeholder="E/24/001">
        <small class="form-text text-muted">@lang('Format: E/YY/XXX')</small>
    </div>
</div>

<div class="form-group row">
    {{-- Department --}}
    <label for="department" class="col-md-2 col-form-label">@lang('Department')</label>
    <div class="col-md-4">
        <select name="department" id="department" class="form-control">
            @foreach ($departmentOptions as $department)
                <option value="{{ $department }}"
                    {{ old('department', $profile->department ?: $defaultDepartment) === $department ? 'selected' : '' }}>
                    {{ $department }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">@lang('Select the department, if you are an external student')</small>
    </div>
</div>
<hr />

{{-- Contact Information --}}
<h5 class="mt-4">@lang('Contact Information')</h5>
<div class="form-group row">
    {{-- Eng Email --}}
    <label for="email" class="col-md-2 col-form-label">@lang('Eng E-mail')*</label>
    <div class="col-md-10">
        <input type="email" name="email" id="email" class="form-control"
            value="{{ old('email', $profile->email) }}" maxlength="255" {{ $selfService ? 'readonly' : '' }}
            required>
        <small class="form-text text-muted">@lang('Format: @eng.pdn.ac.lk / @ce.pdn.ac.lk')</small>
    </div>
</div>
<div class="form-group row">
    {{-- Personal Email --}}
    <label for="personal_email" class="col-md-2 col-form-label">@lang('Personal E-mail')</label>
    <div class="col-md-10">
        <input type="email" name="personal_email" id="personal_email" class="form-control"
            value="{{ old('personal_email', $profile->personal_email) }}" maxlength="255">
        <small class="form-text text-muted">
            @lang('Personal/primary email address')
        </small>
    </div>
</div>
<div class="form-group row">
    {{-- Work Email --}}
    <label for="office_email" class="col-md-2 col-form-label">@lang('Work/Office E-mail')</label>
    <div class="col-md-10">
        <input type="email" name="office_email" id="office_email" class="form-control"
            value="{{ old('office_email', $profile->office_email) }}" maxlength="255">
        <small class="form-text text-muted">@lang('(Optional) Work email address')</small>
    </div>
</div>
<div class="form-group row">
    {{-- Phone Number --}}
    <label for="phone_number" class="col-md-2 col-form-label">@lang('Phone Number')</label>
    <div class="col-md-10">
        <input type="text" name="phone_number" id="phone_number" class="form-control"
            value="{{ old('phone_number', $profile->phone_number) }}" maxlength="50">
        <small class="form-text text-muted">
            @lang('Include full number including the country code, if the number is non Sri Lankan')
        </small>
    </div>
</div>
<div class="form-group row">
    {{-- Resident Address --}}
    <label for="resident_address" class="col-md-2 col-form-label">@lang('Resident Address')</label>
    <div class="col-md-10">
        <textarea name="resident_address" id="resident_address" class="form-control" rows="3">{{ old('resident_address', $profile->resident_address) }}</textarea>
        <small class="form-text text-muted">
            @lang('Use standard address format: Street, City, State/Province, Postal Code, Country')
        </small>
    </div>
</div>
<div class="form-group row">
    {{-- Current Location --}}
    <label for="current_location" class="col-md-2 col-form-label">@lang('Current Location')</label>
    <div class="col-md-10">
        <input type="text" name="current_location" id="current_location" class="form-control"
            value="{{ old('current_location', $profile->current_location) }}" maxlength="255">
        <small class="form-text text-muted">@lang('Current city and country of residence')</small>
    </div>
</div>
<hr />

{{-- Professional Information --}}
<h5 class="mt-4">@lang('Professional Information')</h5>
<div class="form-group row">
    {{-- Position --}}
    <label for="current_position" class="col-md-2 col-form-label">@lang('Current Position')</label>
    <div class="col-md-10">
        <input type="text" name="current_position" id="current_position" class="form-control"
            value="{{ old('current_position', $profile->current_position) }}" maxlength="255">
    </div>
</div>

<div class="form-group row">
    {{-- Current Affiliation --}}
    <label for="current_affiliation_affiliation" class="col-md-2 col-form-label">@lang('Current Affiliation')</label>
    <div class="col-md-6">
        <input type="text" name="current_affiliation[affiliation]" id="current_affiliation_affiliation"
            class="form-control"
            value="{{ old('current_affiliation.affiliation', data_get($profile->current_affiliation, 'affiliation')) }}"
            maxlength="255">
    </div>
    <label for="current_affiliation[start_date]" class="col-md-1 col-form-label">@lang('Since')</label>
    <div class="col-md-3">
        <input type="date" name="current_affiliation[start_date]" class="form-control"
            value="{{ old('current_affiliation.start_date', data_get($profile->current_affiliation, 'start_date')) }}">
    </div>
</div>
<hr />

{{-- Social Profile links --}}
<h5 class="mt-4">@lang('Social Links')</h5>
@foreach ([
        'profile_website' => 'Website',
        'profile_linkedin' => 'LinkedIn',
        'profile_github' => 'GitHub',
        'profile_researchgate' => 'ResearchGate',
        'profile_google_scholar' => 'Google Scholar',
        'profile_orcid' => 'ORCID',
        'profile_facebook' => 'Facebook',
        'profile_twitter' => 'Twitter',
    ] as $field => $label)
    <div class="form-group row">
        <label for="{{ $field }}" class="col-md-2 col-form-label">{{ __($label) }}</label>
        <div class="col-md-10">
            <input type="url" name="{{ $field }}" id="{{ $field }}" class="form-control"
                value="{{ old($field, data_get($profile, $field)) }}" maxlength="255">
        </div>
    </div>
@endforeach
<hr />

{{-- Additional Details - Read Only --}}
@if ($selfService)
    <h5 class="mt-4">@lang('Additional Details')</h5>
    <div class="form-group row">
        {{-- Profile URL --}}
        <label for="profile_url" class="col-md-2 col-form-label">@lang('Profile URL')</label>
        <div class="col-md-10">
            <input type="url" name="profile_url" id="profile_url" class="form-control"
                value="{{ old('profile_url', $profile->profile_url) }}" maxlength="255" readonly>
            <small class="form-text text-muted">@lang('The URL of this profile in the people.ce.pdn.ac.lk')</small>
        </div>
    </div>
    <div class="form-group row">
        {{-- Profile API --}}
        <label for="profile_api" class="col-md-2 col-form-label">@lang('Profile API')</label>
        <div class="col-md-10">
            <input type="url" name="profile_api" id="profile_api" class="form-control"
                value="{{ old('profile_api', $profile->profile_api) }}" maxlength="255" readonly>
            <small class="form-text text-muted">@lang('The API endpoint URL to access this profile data in JSON format')</small>
        </div>
    </div>
@endif
