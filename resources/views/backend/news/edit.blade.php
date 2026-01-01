@extends('backend.layouts.app')

@section('title', __('Edit News'))

@section('content')
    <div>
        {!! Form::open([
            'url' => route('dashboard.news.update', compact('news')),
            'method' => 'put',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
            'id' => 'newsForm',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                News : Edit | {{ $news->title }}
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('title', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('title', $news->title, ['class' => 'form-control', 'required' => true]) !!}
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

                <!-- Published At -->
                <div class="form-group row">
                    {!! Form::label('published_at', 'Publish at*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-3">
                        {!! Form::date('published_at', $news->published_at, ['class' => 'form-control', 'required' => true]) !!}
                        @error('published_at')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- URL -->
                <div class="form-group row">
                    {!! Form::label('url', 'URL*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <div class="d-inline-flex align-items-center flex-nowrap w-100">
                            <span class="me-2"
                                id="url_hint">https://www.ce.pdn.ac.lk/news/{{ $news->published_at }}-&nbsp;</span>
                            <span class="flex-grow-1">{!! Form::text('url', $news->url, ['class' => 'form-control', 'required' => true]) !!}</span>
                        </div>
                        @error('url')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <livewire:backend.richtext-editor-component name="description"
                            value="{{ old('description', $news->description ?? '') }}" />

                        <div id="description-error" class="text-danger mt-1" style="display: none;"></div>
                        <div class="col-md-12">
                            @error('description')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled*', ['class' => 'col-md-2 form-check-label']) !!}
                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="checkbox" id="checkEnable" name="enabled" value={{ $news->enable ? 'checked' : '""' }}
                            class="form-check-input checkbox-lg" {{ $news->enabled == 1 ? 'checked' : '' }} />
                        <label class="form-check-label" for="checkEnable">&nbsp;</label>
                        @error('enabled')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Link URL -->
                <div class="form-group row">
                    {!! Form::label('link_url', 'Link URL', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('link_url', $news->link_url, ['class' => 'form-control']) !!}
                        @error('link_url')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Link Caption -->
                <div class="form-group row">
                    {!! Form::label('link_caption', 'Link Caption', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('link_caption', $news->link_caption, ['class' => 'form-control']) !!}
                        @error('link_caption')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-end ms-2', 'id' => 'submit-button']) !!}
                @if (config('gallery.enabled'))
                    <a href="{{ route('dashboard.news.gallery.index', $news) }}"
                        class="btn btn-secondary btn-w-150  float-end">
                        <i class="fas fa-images me-2"></i> Manage Gallery
                    </a>
                @endif
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>

    <script>
        document.getElementById('published_at').addEventListener('change', function() {
            document.getElementById('url_hint').textContent =
                `https://www.ce.pdn.ac.lk/news/${this.value ?? 'yyyy-mm-dd'}-`;
        });
    </script>
@endsection
