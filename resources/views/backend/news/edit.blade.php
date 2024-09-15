@extends('backend.layouts.app')

@section('title', __('Edit News'))

@push('after-styles')
    <!-- Include Quill library -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endpush

@section('content')
    <div>
        {!! Form::open([
            'url' => route('dashboard.news.update', compact('news')),
            'method' => 'put',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                News : Edit | {{ $news->title }}
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('message', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('title', $news->title, [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                        @error('title')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Published At -->
                <div class="form-group row">
                    {!! Form::label('published_at', 'Publish at*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-3">
                        {!! Form::date('published_at', $news->published_at, ['class' => 'form-control', 'required' => true]) !!}
                        @error('published_at')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- URL -->
                <div class="form-group row">
                    {!! Form::label('url', 'URL*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <div class="d-inline-flex align-items-center flex-nowrap w-100">
                            <span class="me-2">https://ce.pdn.ac.lk/news/{{ $news->published_at }}-&nbsp;</span>
                            <span class="flex-grow-1"> {!! Form::text('url', $news->url, ['class' => 'form-control', 'required' => true]) !!}</span>
                        </div>
                        @error('url')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        <div id="editor-container" style="height: auto; min-height: 200px; border: 1px solid #ced4da; border-radius: .25rem;">{!! $news->description !!}</div>
                        <textarea name="description" id="description" style="display:none;" ></textarea>
                        <div id="description-error" class="text-danger mt-1" style="display: none;"></div> 
                        @error('description')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Image -->
                <div class="form-group row">
                    {!! Form::label('image', 'Image', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        <div>{!! Form::file('image', ['accept' => 'image/*']) !!}
                            @error('image')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </div>

                        <!-- Image preview -->
                        <img class="mt-3" src="{{ $news->thumbURL() }}" alt="Image preview"
                            style="max-width: 150px; max-height: 150px;" />
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row ">
                    {!! Form::label('enabled', 'Enabled*', ['class' => 'col-md-2 form-check-label']) !!}

                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="checkbox" id="checkEnable" name="enabled" value={{ $news->enable ? 'checked' : '""' }}
                            class="form-check-input checkbox-lg" {{ $news->enabled == 1 ? 'checked' : '' }} />
                        <label class="form-check-label" for="checkEnable">Visibility</label>
                        @error('enabled')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- link URL -->
                <div class="form-group row">
                    {!! Form::label('link_url', 'Link URL', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_url', $news->link_url, [
                            'class' => 'form-control',
                        ]) !!}
                        @error('link_url')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- link Caption -->
                <div class="form-group row">
                    {!! Form::label('link_caption', 'Link Caption', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_caption', $news->link_caption, [
                            'class' => 'form-control',
                        ]) !!}
                        @error('link_caption')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

            </x-slot>
            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>
@endsection
