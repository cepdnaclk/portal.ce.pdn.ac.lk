@extends('backend.layouts.app')

@section('title', __('Edit Semester'))

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
        {!! Form::model($semester, [
            'url' => route('dashboard.semesters.update', $semester->id),
            'method' => 'put',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                Semester : Edit | {{ $semester->title }}
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('title', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('title', null, [
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
                        {!! Form::select('academic_program', \App\Domains\Semester\Models\Semester::getAcademicPrograms(), null, ['class' => 'form-control', 'required' => true]) !!}
                        @error('academic_program')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Version -->
                <div class="form-group row">
                    {!! Form::label('version', 'Version*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::select('version', \App\Domains\Semester\Models\Semester::getVersions(), null, ['class' => 'form-control', 'required' => true]) !!}
                        @error('version')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
                        @error('description')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- URL -->
                <div class="form-group row">
                    {!! Form::label('url', 'URL', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('url', null, ['class' => 'form-control']) !!}
                        @error('url')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary float-right']) !!}
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>
@endsection
