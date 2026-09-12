{{-- Shared profile form fields; expects an optional $profile when editing. --}}
@php
    use App\Domains\Profile\Models\UserProfileLink;
    use App\Domains\Profile\Models\UserProfileType;
    use App\Domains\Profile\Models\UserProfile;

    $assignedTypes = isset($profile) ? $profile->profileTypes->keyBy('type') : collect();
    $profileLinks = isset($profile) ? $profile->links->pluck('url', 'type') : collect();

    $fieldValue = function ($field) use ($profile) {
        $value = old($field, $profile->{$field} ?? '');

        return is_array($value) ? implode(', ', $value) : $value;
    };
    $typeAttr = function ($type, $key) use ($assignedTypes) {
        $value = old(
            "types.$type.attributes.$key",
            $assignedTypes->get($type)?->getAttribute('attributes')[$key] ?? '',
        );

        return is_array($value) ? implode(', ', $value) : $value;
    };
@endphp

<h5 class="border-bottom pb-2 mb-3">{{ __('Identity & Names') }}</h5>

<div class="form-group row">
    {!! Form::label('email', __('Email') . '*', ['class' => 'col-md-2 col-form-label']) !!}
    <div class="col-md-10">
        {!! Form::email('email', $fieldValue('email'), [
            'class' => 'form-control',
            'required' => true,
            'autocomplete' => 'email',
        ]) !!}
        @error('email')
            <strong class="text-danger">{{ $message }}</strong>
        @enderror
    </div>
</div>

<div class="form-group row">
    {!! Form::label('alternate_email', __('Alternate Email'), ['class' => 'col-md-2 col-form-label']) !!}
    <div class="col-md-10">
        {!! Form::email('alternate_email', $fieldValue('alternate_email'), [
            'class' => 'form-control',
            'autocomplete' => 'email',
        ]) !!}
        @error('alternate_email')
            <strong class="text-danger">{{ $message }}</strong>
        @enderror
    </div>
</div>

<div class="form-group row">
    {!! Form::label('honorific', __('Honorific'), ['class' => 'col-md-2 col-form-label']) !!}
    <div class="col-md-3">
        {!! Form::select('honorific', UserProfile::HONORIFIC_OPTIONS, $fieldValue('honorific'), [
            'class' => 'form-select',
            'placeholder' => __('Select an honorific'),
        ]) !!}
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
        {!! Form::label($field, $label, ['class' => 'col-md-2 col-form-label']) !!}
        <div class="col-md-10">
            {!! Form::text($field, $fieldValue($field), ['class' => 'form-control']) !!}
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
    ] as $field => $label)
    <div class="form-group row">
        {!! Form::label($field, $label, ['class' => 'col-md-2 col-form-label']) !!}
        <div class="col-md-10">
            {!! Form::text($field, $fieldValue($field), ['class' => 'form-control']) !!}
            @error($field)
                <strong class="text-danger">{{ $message }}</strong>
            @enderror
        </div>
    </div>
@endforeach

<div class="form-group row">
    {!! Form::label('interests', __('Interests'), ['class' => 'col-md-2 col-form-label']) !!}
    <div class="col-md-10">
        {!! Form::textarea('interests', $fieldValue('interests'), [
            'class' => 'form-control',
            'placeholder' => __('Comma-separated values'),
            'rows' => 3,
        ]) !!}
        @error('interests')
            <strong class="text-danger">{{ $message }}</strong>
        @enderror
    </div>
</div>

<div class="form-group row">
    {!! Form::label('profile_image', __('Profile Picture'), ['class' => 'col-md-2 col-form-label']) !!}
    <div class="col-md-10">
        @if (isset($profile) && $profile->profileImageUrl())
            <div class="mb-2">
                <img src="{{ $profile->profileImageUrl() }}" class="img-thumbnail" style="max-height: 150px;"
                    alt="{{ __('Current profile picture') }}" />
            </div>
        @endif
        {!! Form::file('profile_image', ['class' => 'form-control', 'accept' => 'image/jpeg']) !!}
        <small class="form-text text-muted">
            {{ __('JPEG only. Maximum size: :size MB. Minimum dimensions: :width×:height pixels.', [
                'size' => config('profile.image.max_file_size') / 1024,
                'width' => config('profile.image.min_width'),
                'height' => config('profile.image.min_height'),
            ]) }}
        </small>
        @error('profile_image')
            <strong class="text-danger d-block">{{ $message }}</strong>
        @enderror
    </div>
</div>

<h5 class="border-bottom pb-2 mt-4 mb-3">{{ __('Profile Types') }}</h5>

@error('types')
    <x-utils.alert type="danger" :dismissable="false">{{ $message }}</x-utils.alert>
@enderror

<div class="form-group row">
    <div class="col-md-2 col-form-label">{{ __('Student') }}</div>
    <div class="col-md-10">
        <div class="border rounded p-3">
            <div class="form-check form-switch mb-3">
                {!! Form::checkbox(
                    'types[STUDENT][assigned]',
                    1,
                    (bool) old('types.STUDENT.assigned', $assignedTypes->has(UserProfileType::TYPE_STUDENT)),
                    ['class' => 'form-check-input', 'id' => 'type_student'],
                ) !!}
                {!! Form::label('type_student', __('Assign student profile'), ['class' => 'ms-4 form-check-label']) !!}
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    {!! Form::label('student_reg_number', __('Registration Number'), ['class' => 'form-label']) !!}
                    {!! Form::text('types[STUDENT][attributes][reg_number]', $typeAttr('STUDENT', 'reg_number'), [
                        'class' => 'form-control',
                        'id' => 'student_reg_number',
                        'placeholder' => 'E/00/000',
                    ]) !!}
                    @error('types.STUDENT.attributes.reg_number')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    {!! Form::label('student_batch', __('Batch'), ['class' => 'form-label']) !!}
                    {!! Form::text('types[STUDENT][attributes][batch]', $typeAttr('STUDENT', 'batch'), [
                        'class' => 'form-control',
                        'id' => 'student_batch',
                    ]) !!}
                    @error('types.STUDENT.attributes.batch')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-12 mb-0">
                    {!! Form::label('student_department', __('Department'), ['class' => 'form-label']) !!}
                    {!! Form::select(
                        'types[STUDENT][attributes][department]',
                        UserProfile::DEPARTMENT_OPTIONS,
                        $typeAttr('STUDENT', 'department'),
                        [
                            'class' => 'form-select',
                            'id' => 'student_department',
                            'placeholder' => __('Select a department'),
                        ],
                    ) !!}
                    @error('types.STUDENT.attributes.department')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-2 col-form-label">{{ __('Academic Staff') }}</div>
    <div class="col-md-10">
        <div class="border rounded p-3">
            <div class="form-check form-switch mb-3">
                {!! Form::checkbox(
                    'types[ACADEMIC_STAFF][assigned]',
                    1,
                    (bool) old('types.ACADEMIC_STAFF.assigned', $assignedTypes->has(UserProfileType::TYPE_ACADEMIC_STAFF)),
                    ['class' => 'form-check-input', 'id' => 'type_academic_staff'],
                ) !!}
                {!! Form::label('type_academic_staff', __('Assign academic staff profile'), [
                    'class' => 'ms-4 form-check-label',
                ]) !!}
            </div>
            <div class="row">
                <div class="col-md-12 mb-0">
                    {!! Form::label('academic_designation', __('Designation'), ['class' => 'form-label']) !!}
                    {!! Form::text('types[ACADEMIC_STAFF][attributes][designation]', $typeAttr('ACADEMIC_STAFF', 'designation'), [
                        'class' => 'form-control',
                        'id' => 'academic_designation',
                    ]) !!}
                    @error('types.ACADEMIC_STAFF.attributes.designation')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-6 mt-3 mb-0">
                    {!! Form::label('academic_start_date', __('Start Date'), ['class' => 'form-label']) !!}
                    {!! Form::date('types[ACADEMIC_STAFF][attributes][start_date]', $typeAttr('ACADEMIC_STAFF', 'start_date'), [
                        'class' => 'form-control',
                        'id' => 'academic_start_date',
                    ]) !!}
                    @error('types.ACADEMIC_STAFF.attributes.start_date')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-6 mt-3 mb-0">
                    {!! Form::label('academic_end_date', __('End Date'), ['class' => 'form-label']) !!}
                    {!! Form::date('types[ACADEMIC_STAFF][attributes][end_date]', $typeAttr('ACADEMIC_STAFF', 'end_date'), [
                        'class' => 'form-control',
                        'id' => 'academic_end_date',
                    ]) !!}
                    @error('types.ACADEMIC_STAFF.attributes.end_date')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-2 col-form-label">{{ __('External') }}</div>
    <div class="col-md-10">
        <div class="border rounded p-3">
            <div class="form-check form-switch mb-3">
                {!! Form::checkbox(
                    'types[EXTERNAL][assigned]',
                    1,
                    (bool) old('types.EXTERNAL.assigned', $assignedTypes->has(UserProfileType::TYPE_EXTERNAL)),
                    ['class' => 'form-check-input', 'id' => 'type_external'],
                ) !!}
                {!! Form::label('type_external', __('Assign external profile'), ['class' => 'ms-4 form-check-label']) !!}
            </div>
            <div class="row">
                @foreach (['affiliation' => __('Affiliation'), 'position' => __('Position')] as $field => $label)
                    <div class="col-md-6 mb-0">
                        {!! Form::label("external_$field", $label, ['class' => 'form-label']) !!}
                        {!! Form::text("types[EXTERNAL][attributes][$field]", $typeAttr('EXTERNAL', $field), [
                            'class' => 'form-control',
                            'id' => "external_$field",
                        ]) !!}
                        @error("types.EXTERNAL.attributes.$field")
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<h5 class="border-bottom pb-2 mt-4 mb-3">{{ __('Links') }}</h5>

@error('links')
    <x-utils.alert type="danger" :dismissable="false">{{ $message }}</x-utils.alert>
@enderror

@foreach (UserProfileLink::LINK_TYPE_LABELS as $linkType => $linkLabel)
    <div class="form-group row">
        {!! Form::label("link_$linkType", $linkLabel, ['class' => 'col-md-2 col-form-label']) !!}
        <div class="col-md-10">
            {!! Form::url("links[$linkType]", old("links.$linkType", $profileLinks->get($linkType)), [
                'class' => 'form-control',
                'id' => "link_$linkType",
                'placeholder' => 'https://',
            ]) !!}
            @error("links.$linkType")
                <strong class="text-danger">{{ $message }}</strong>
            @enderror
        </div>
    </div>
@endforeach
