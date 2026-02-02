@extends('backend.layouts.app')

@section('title', __('Edit Article'))

@section('content')
    <div>
        {!! Form::open([
            'url' => route('dashboard.article.update', compact('article')),
            'method' => 'put',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
            'id' => 'articleForm',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                Article : Edit | {{ $article->title }}
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('title', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('title', $article->title, ['class' => 'form-control', 'required' => true]) !!}
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
                        {!! Form::text(
                            'categories',
                            old('categories', $article->categories_json ? implode(', ', $article->categories_json) : ''),
                            [
                                'class' => 'form-control',
                                'placeholder' => 'e.g. research, alumni, awards',
                            ],
                        ) !!}
                        @error('categories')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled*', ['class' => 'col-md-2 form-check-label']) !!}
                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="checkbox" id="checkEnable" name="enabled"
                            value={{ $article->enable ? 'checked' : '""' }} class="form-check-input checkbox-lg"
                            {{ $article->enabled == 1 ? 'checked' : '' }} />
                        <label class="form-check-label" for="checkEnable">&nbsp;</label>
                        @error('enabled')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <input type="hidden" id="content_images_json" name="content_images_json"
                    value="{{ old('content_images_json', json_encode($article->content_images_json ?? [])) }}" />

                <!-- Content -->
                <div class="form-group row">
                    {!! Form::label('content', 'Content*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <livewire:backend.richtext-editor-component name="content"
                            value="{{ old('content', $article->content ?? '') }}"
                            upload-url="{{ route('dashboard.article.content-images.upload') }}"
                            content-images-input="content_images_json" />

                        <div id="content-error" class="text-danger mt-1" style="display: none;"></div>
                        <div class="col-md-12">
                            @error('content')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-end ms-2', 'id' => 'submit-button']) !!}
                @if (config('gallery.enabled'))
                    <a href="{{ route('dashboard.article.gallery.index', $article) }}"
                        class="btn btn-secondary btn-w-150  float-end">
                        <i class="fas fa-images me-2"></i> Manage Gallery
                    </a>
                @endif
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>
@endsection
