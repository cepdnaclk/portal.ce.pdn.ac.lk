@extends('backend.layouts.app')

@section('title', __('Create Semester'))

@push('after-styles')
    <style>
        /* Style dropdown fields to ensure arrows are visible */
        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 140 140"><polygon points="70,140 140,0 0,0" style="fill:%23000" /></svg>') no-repeat right 10px center;
            background-size: 10px;
            padding-right: 30px;
        }
    </style>
@endpush


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
                                'class' => 'form-control',
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
                    {!! Form::label('version', 'Version*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::select('version', \App\Domains\AcademicProgram\Semester\Models\Semester::getVersions(), null, [
                            'class' => 'form-control',
                            'placeholder' => 'Select Version',
                            'required' => true,
                        ]) !!}
                        @error('version')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
                        @error('description')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- URL -->
                <div class="form-group row">
                    {!! Form::label('url', 'URL', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <div class="d-inline-flex align-items-center flex-nowrap w-100">
                            <span class="me-2" id="url_hint">
                                https://www.ce.pdn.ac.lk/academics/{academic_program}/semesters/&nbsp;&nbsp;</span>
                            <span class="flex-grow-1">
                                {!! Form::text('url', old('url', ''), ['class' => 'form-control', 'required' => true]) !!}
                            </span>
                        </div>
                        @error('url')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>


                </div>
                <script>
                    // TODO convert to jQuery and add the version 
                    document.getElementById('academic_program').addEventListener('change', function() {
                        let selectedProgram = this.value.toLowerCase();
                        const urlHint = document.getElementById('url_hint');
                        urlHint.textContent =
                            `https://www.ce.pdn.ac.lk/academics/${selectedProgram ? selectedProgram: '{academic_program}'}/semesters/`;

                    });
                </script>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary float-right']) !!}
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>
@endsection
