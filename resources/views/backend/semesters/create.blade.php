@extends('backend.layouts.app')

@section('title', __('Create Semester'))

@section('content')
    <div>
        {!! Form::open([
            'url' => route('dashboard.semesters.store'),
            'method' => 'post',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                Semester : Create
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('title', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('title', old('title'), [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                        @error('title')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Academic Program -->
                <div class="form-group row">
                    {!! Form::label('academic_program', 'Academic Program*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::select(
                            'academic_program',
                            \App\Domains\AcademicProgram\Semester\Models\Semester::getAcademicPrograms(),
                            null,
                            [
                                'class' => 'form-select',
                                'placeholder' => 'Select Academic Program',
                                'required' => true,
                                'id' => 'academic_program',
                            ],
                        ) !!}
                        @error('academic_program')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Version -->
                <div class="form-group row">
                    {!! Form::label('version', 'Curriculum*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-9">
                        {{-- TODO make this depends from the Academic Program --}}
                        {!! Form::select('version', \App\Domains\AcademicProgram\Semester\Models\Semester::getVersions(), null, [
                            'class' => 'form-select',
                            'placeholder' => 'Select Version',
                            'required' => true,
                        ]) !!}
                        @error('version')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>

                    <div class="col-md-1">
                        <x-backend.taxonomy_tooltip
                            edit-url="{{ route('dashboard.taxonomy.alias', ['code' => 'academic_program_undergraduate']) }}"
                            placement="auto">
                        </x-backend.taxonomy_tooltip>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
                        @error('description')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- URL -->
                <div class="form-group row">
                    {!! Form::label('url', 'URL*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <div class="d-inline-flex align-items-center flex-nowrap w-100">
                            <span class="me-2" id="url_hint">
                                https://www.ce.pdn.ac.lk/academics/{academic_program}/semesters/</span>
                            <span class="flex-grow-1">
                                {!! Form::text('url', old('url', ''), ['class' => 'form-control', 'required' => true]) !!}
                            </span>
                        </div>
                        @error('url')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>


                </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary float-right btn-w-150']) !!}
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>

    <script>
        document.getElementById('academic_program').addEventListener('change', function() {
            const selectedProgram = document.getElementById('academic_program').value;
            const version = document.getElementById('version').value.toLowerCase(); // To be used in the URL
            const urlHint = document.getElementById('url_hint').textContent =
                `https://www.ce.pdn.ac.lk/academics/${selectedProgram ? selectedProgram: '{academic_program}'}/semesters/`;
        });
    </script>
@endsection
