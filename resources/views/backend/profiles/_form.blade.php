{{-- Shared profile form fields; expects an optional $profile when editing. --}}
@php
    use App\Domains\Profile\Models\UserProfileLink;
    use App\Domains\Profile\Models\UserProfileType;
    use App\Domains\Profile\Models\UserProfile;

    $assignedTypes = isset($profile) ? $profile->profileTypes->keyBy('type') : collect();
    $profileLinks = isset($profile) ? $profile->links->pluck('url', 'type') : collect();

    $fieldValue = fn($field) => old($field, $profile->{$field} ?? '');
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
        'profile_image' => __('Profile Image URL'),
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
                <div class="col-md-6 mb-0">
                    {!! Form::label('student_department', __('Department'), ['class' => 'form-label']) !!}
                    {!! Form::text('types[STUDENT][attributes][department]', $typeAttr('STUDENT', 'department'), [
                        'class' => 'form-control',
                        'id' => 'student_department',
                    ]) !!}
                    @error('types.STUDENT.attributes.department')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-6 mb-0">
                    {!! Form::label('student_interests', __('Interests'), ['class' => 'form-label']) !!}
                    {!! Form::text('types[STUDENT][attributes][interests]', $typeAttr('STUDENT', 'interests'), [
                        'class' => 'form-control',
                        'id' => 'student_interests',
                        'placeholder' => __('Comma-separated values'),
                    ]) !!}
                    @error('types.STUDENT.attributes.interests')
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
                <div class="col-md-6 mb-0">
                    {!! Form::label('academic_designation', __('Designation'), ['class' => 'form-label']) !!}
                    {!! Form::text('types[ACADEMIC_STAFF][attributes][designation]', $typeAttr('ACADEMIC_STAFF', 'designation'), [
                        'class' => 'form-control',
                        'id' => 'academic_designation',
                    ]) !!}
                    @error('types.ACADEMIC_STAFF.attributes.designation')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-6 mb-0">
                    {!! Form::label('academic_research_interests', __('Research Interests'), ['class' => 'form-label']) !!}
                    {!! Form::text(
                        'types[ACADEMIC_STAFF][attributes][research_interests]',
                        $typeAttr('ACADEMIC_STAFF', 'research_interests'),
                        [
                            'class' => 'form-control',
                            'id' => 'academic_research_interests',
                            'placeholder' => __('Comma-separated values'),
                        ],
                    ) !!}
                    @error('types.ACADEMIC_STAFF.attributes.research_interests')
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
                @foreach ([
        'affiliation' => __('Affiliation'),
        'position' => __('Position'),
        'interests' => __('Interests'),
    ] as $field => $label)
                    <div class="col-md-4 mb-0">
                        {!! Form::label("external_$field", $label, ['class' => 'form-label']) !!}
                        {!! Form::text("types[EXTERNAL][attributes][$field]", $typeAttr('EXTERNAL', $field), [
                            'class' => 'form-control',
                            'id' => "external_$field",
                            'placeholder' => $field === 'interests' ? __('Comma-separated values') : null,
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
