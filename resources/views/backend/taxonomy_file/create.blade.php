@extends('backend.layouts.app')

@section('title', __('Upload Taxonomy File'))

@section('content')
    <div x-data="{ metadata: {} }">

        {!! Form::open([
            'url' => route('dashboard.taxonomy-files.store'),
            'method' => 'post',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        @csrf

        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy File : Upload') }}
            </x-slot>

            <x-slot name="body">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: left; text-decoration: none;">Basic</h5>

                        <!-- Taxonomy Name -->
                        <div class="row">
                            {!! Form::label('file_name', 'File Name*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::text('file_name', '', [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'placeholder' => 'Enter the preferred file name to be displayed',
                                ]) !!}
                                @error('file_name')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        {{-- File --}}
                        <div class="row">
                            {!! Form::label('file', "File* (10 MB max, supports $supportedExtensions only)", [
                                'class' => 'col-form-label',
                            ]) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::file('file', ['class' => 'form-control']) !!}
                                @error('file')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        {{-- Taxonomy selector --}}
                        @isset($taxonomies)
                            <div class="row">
                                {!! Form::label('taxonomy_id', 'Related Taxonomy (optional)', ['class' => 'col-form-label']) !!}
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    {!! Form::select('taxonomy_id', $taxonomies->pluck('name', 'id')->prepend(__('— none —'), ''), null, [
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('taxonomy_id')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>
                        @endisset
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit(__('Upload'), ['class' => 'btn btn-primary btn-w-150 float-right']) !!}
            </x-slot>
        </x-backend.card>

        {!! Form::close() !!}
    </div>
@endsection
