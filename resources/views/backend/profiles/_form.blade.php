{{-- Shared profile form fields; expects an optional $profile when editing --}}
@php
    use App\Domains\Profile\Models\UserProfileLink;
    use App\Domains\Profile\Models\UserProfileType;

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

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Identity &amp; Names</h5>

        <div class="row">
            <div class="col-md-6 mb-3">
                {!! Form::label('email', 'Email*', ['class' => 'form-label']) !!}
                {!! Form::email('email', $fieldValue('email'), ['class' => 'form-control', 'required' => true]) !!}
                @error('email')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                {!! Form::label('alternate_email', 'Alternate Email', ['class' => 'form-label']) !!}
                {!! Form::email('alternate_email', $fieldValue('alternate_email'), ['class' => 'form-control']) !!}
                @error('alternate_email')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-2 mb-3">
                {!! Form::label('honorific', 'Honorific', ['class' => 'form-label']) !!}
                {!! Form::text('honorific', $fieldValue('honorific'), ['class' => 'form-control', 'maxlength' => 20]) !!}
                @error('honorific')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-5 mb-3">
                {!! Form::label('full_name', 'Full Name', ['class' => 'form-label']) !!}
                {!! Form::text('full_name', $fieldValue('full_name'), ['class' => 'form-control']) !!}
                @error('full_name')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-5 mb-3">
                {!! Form::label('name_with_initials', 'Name with Initials', ['class' => 'form-label']) !!}
                {!! Form::text('name_with_initials', $fieldValue('name_with_initials'), ['class' => 'form-control']) !!}
                @error('name_with_initials')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                {!! Form::label('preferred_short_name', 'Preferred Short Name', ['class' => 'form-label']) !!}
                {!! Form::text('preferred_short_name', $fieldValue('preferred_short_name'), ['class' => 'form-control']) !!}
                @error('preferred_short_name')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                {!! Form::label('preferred_long_name', 'Preferred Long Name', ['class' => 'form-label']) !!}
                {!! Form::text('preferred_long_name', $fieldValue('preferred_long_name'), ['class' => 'form-control']) !!}
                @error('preferred_long_name')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Location &amp; Affiliation</h5>

        <div class="row">
            <div class="col-md-6 mb-3">
                {!! Form::label('location', 'Location', ['class' => 'form-label']) !!}
                {!! Form::text('location', $fieldValue('location'), ['class' => 'form-control']) !!}
                @error('location')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                {!! Form::label('current_affiliation', 'Current Affiliation', ['class' => 'form-label']) !!}
                {!! Form::text('current_affiliation', $fieldValue('current_affiliation'), ['class' => 'form-control']) !!}
                @error('current_affiliation')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                {!! Form::label('current_position', 'Current Position', ['class' => 'form-label']) !!}
                {!! Form::text('current_position', $fieldValue('current_position'), ['class' => 'form-control']) !!}
                @error('current_position')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                {!! Form::label('profile_image', 'Profile Image URL', ['class' => 'form-label']) !!}
                {!! Form::text('profile_image', $fieldValue('profile_image'), ['class' => 'form-control']) !!}
                @error('profile_image')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Profile Types</h5>
        @error('types')
            <strong class="text-danger d-block mb-2">{{ $message }}</strong>
        @enderror

        {{-- Student --}}
        <div class="border rounded p-3 mb-3">
            <div class="form-check mb-2">
                {!! Form::checkbox(
                    'types[STUDENT][assigned]',
                    1,
                    (bool) old('types.STUDENT.assigned', $assignedTypes->has(UserProfileType::TYPE_STUDENT)),
                    ['class' => 'form-check-input', 'id' => 'type_student'],
                ) !!}
                {!! Form::label('type_student', 'Student', ['class' => 'form-check-label fw-bold']) !!}
            </div>

            <div class="row">
                <div class="col-md-3 mb-2">
                    {!! Form::label('types[STUDENT][attributes][reg_number]', 'Reg. Number (E/nn/nnn)', ['class' => 'form-label']) !!}
                    {!! Form::text('types[STUDENT][attributes][reg_number]', $typeAttr('STUDENT', 'reg_number'), [
                        'class' => 'form-control',
                    ]) !!}
                    @error('types.STUDENT.attributes.reg_number')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-3 mb-2">
                    {!! Form::label('types[STUDENT][attributes][batch]', 'Batch', ['class' => 'form-label']) !!}
                    {!! Form::text('types[STUDENT][attributes][batch]', $typeAttr('STUDENT', 'batch'), ['class' => 'form-control']) !!}
                    @error('types.STUDENT.attributes.batch')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-3 mb-2">
                    {!! Form::label('types[STUDENT][attributes][department]', 'Department', ['class' => 'form-label']) !!}
                    {!! Form::text('types[STUDENT][attributes][department]', $typeAttr('STUDENT', 'department'), [
                        'class' => 'form-control',
                    ]) !!}
                </div>
                <div class="col-md-3 mb-2">
                    {!! Form::label('types[STUDENT][attributes][interests]', 'Interests (comma separated)', [
                        'class' => 'form-label',
                    ]) !!}
                    {!! Form::text('types[STUDENT][attributes][interests]', $typeAttr('STUDENT', 'interests'), [
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
        </div>

        {{-- Academic Staff --}}
        <div class="border rounded p-3 mb-3">
            <div class="form-check mb-2">
                {!! Form::checkbox(
                    'types[ACADEMIC_STAFF][assigned]',
                    1,
                    (bool) old('types.ACADEMIC_STAFF.assigned', $assignedTypes->has(UserProfileType::TYPE_ACADEMIC_STAFF)),
                    ['class' => 'form-check-input', 'id' => 'type_academic_staff'],
                ) !!}
                {!! Form::label('type_academic_staff', 'Academic Staff', ['class' => 'form-check-label fw-bold']) !!}
            </div>

            <div class="row">
                <div class="col-md-6 mb-2">
                    {!! Form::label('types[ACADEMIC_STAFF][attributes][designation]', 'Designation', ['class' => 'form-label']) !!}
                    {!! Form::text('types[ACADEMIC_STAFF][attributes][designation]', $typeAttr('ACADEMIC_STAFF', 'designation'), [
                        'class' => 'form-control',
                    ]) !!}
                    @error('types.ACADEMIC_STAFF.attributes.designation')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="col-md-6 mb-2">
                    {!! Form::label(
                        'types[ACADEMIC_STAFF][attributes][research_interests]',
                        'Research Interests (comma separated)',
                        ['class' => 'form-label'],
                    ) !!}
                    {!! Form::text(
                        'types[ACADEMIC_STAFF][attributes][research_interests]',
                        $typeAttr('ACADEMIC_STAFF', 'research_interests'),
                        ['class' => 'form-control'],
                    ) !!}
                </div>
            </div>
        </div>

        {{-- External --}}
        <div class="border rounded p-3">
            <div class="form-check mb-2">
                {!! Form::checkbox(
                    'types[EXTERNAL][assigned]',
                    1,
                    (bool) old('types.EXTERNAL.assigned', $assignedTypes->has(UserProfileType::TYPE_EXTERNAL)),
                    ['class' => 'form-check-input', 'id' => 'type_external'],
                ) !!}
                {!! Form::label('type_external', 'External', ['class' => 'form-check-label fw-bold']) !!}
            </div>

            <div class="row">
                <div class="col-md-4 mb-2">
                    {!! Form::label('types[EXTERNAL][attributes][affiliation]', 'Affiliation', ['class' => 'form-label']) !!}
                    {!! Form::text('types[EXTERNAL][attributes][affiliation]', $typeAttr('EXTERNAL', 'affiliation'), [
                        'class' => 'form-control',
                    ]) !!}
                </div>
                <div class="col-md-4 mb-2">
                    {!! Form::label('types[EXTERNAL][attributes][position]', 'Position', ['class' => 'form-label']) !!}
                    {!! Form::text('types[EXTERNAL][attributes][position]', $typeAttr('EXTERNAL', 'position'), [
                        'class' => 'form-control',
                    ]) !!}
                </div>
                <div class="col-md-4 mb-2">
                    {!! Form::label('types[EXTERNAL][attributes][interests]', 'Interests (comma separated)', [
                        'class' => 'form-label',
                    ]) !!}
                    {!! Form::text('types[EXTERNAL][attributes][interests]', $typeAttr('EXTERNAL', 'interests'), [
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Links</h5>
        @error('links')
            <strong class="text-danger d-block mb-2">{{ $message }}</strong>
        @enderror

        <div class="row">
            @foreach (UserProfileLink::LINK_TYPE_LABELS as $linkType => $linkLabel)
                <div class="col-md-6 mb-3">
                    {!! Form::label("links[$linkType]", $linkLabel, ['class' => 'form-label']) !!}
                    {!! Form::text("links[$linkType]", old("links.$linkType", $profileLinks->get($linkType)), [
                        'class' => 'form-control',
                        'placeholder' => 'https://…',
                    ]) !!}
                    @error("links.$linkType")
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>
</div>
