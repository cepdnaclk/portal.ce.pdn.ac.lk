@extends('backend.layouts.app')

@section('title', __('Create Taxonomy Page'))

@section('content')
    <div x-data="{ metadata: {} }">

        {!! Form::open([
            'url' => route('dashboard.taxonomy-pages.store'),
            'method' => 'post',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        @csrf

        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy Page : Create') }}
            </x-slot>

            <x-slot name="body">
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Slug -->
                        <div class="row">
                            {!! Form::label('slug', 'Slug*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::text('slug', '', [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'max_length' => 255,
                                    'placeholder' => 'Enter an unique slug (e.g., about-us) as the page identifier',
                                ]) !!}
                                @error('slug')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            {!! Form::label('html', 'HTML Content*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <livewire:backend.richtext-editor-component name="html" value=""
                                    style="height: 300px;" />
                                @error('html')
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
                {!! Form::submit(__('Create'), ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
                <a href="{{ route('dashboard.taxonomy-pages.index') }}"
                    class="btn btn-light btn-outline-secondary btn-w-150 float-end mr-2">Back</a>
            </x-slot>
        </x-backend.card>

        {!! Form::close() !!}
    </div>
@endsection
