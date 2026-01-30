@extends('backend.layouts.app')

@section('title', __('Create Article'))

@section('content')
    <div>
        {!! Form::open([
            'url' => route('dashboard.article.store'),
            'method' => 'post',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                Article : Create
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('title', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('title', '', ['class' => 'form-control']) !!}
                        @error('title')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Tenant -->
                <div class="form-group row">
                    {!! Form::label('tenant_id', 'Tenant*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::select('tenant_id', $tenants->pluck('name', 'id'), $selectedTenantId, [
                            'class' => 'form-select',
                            'required' => true,
                            'placeholder' => '',
                        ]) !!}
                        @error('tenant_id')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Categories -->
                <div class="form-group row">
                    {!! Form::label('categories', 'Categories', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('categories', '', [
                            'class' => 'form-control',
                            'placeholder' => 'e.g. research, alumni, awards',
                        ]) !!}
                        @error('categories')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <input type="hidden" id="content_images_json" name="content_images_json"
                    value="{{ old('content_images_json', json_encode([])) }}" />

                <!-- Content -->
                <div class="form-group row">
                    {!! Form::label('content', 'Content*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <livewire:backend.richtext-editor-component name="content" value=""
                            upload-url="{{ route('dashboard.article.content-images.upload') }}"
                            content-images-input="content_images_json" />

                        <div id="content-error" class="text-danger mt-1" style="display: none;"></div>
                        <div class="col-md-12">
                            @error('content')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
            </x-slot>

        </x-backend.card>

        {!! Form::close() !!}
    </div>
@endsection
