@extends('backend.layouts.app')

@section('title', __('Edit News'))

@push('after-styles')
    <!-- Include Quill library -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endpush

@push('before-scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
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
                    {!! Form::label('title', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('title', $news->title, ['class' => 'form-control', 'required' => true]) !!}
                        @error('title')
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
                            <span class="me-2">https://ce.pdn.ac.lk/news/{{ $news->published_at }}-&nbsp;</span>
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
                        <div x-data="{ content: '{{$news->description}}'  }" x-init="
                            (() => {
                                const quill = new Quill($refs.editor, {
                                    theme: 'snow',
                                    modules: {
                                        toolbar: [
                                            ['bold', 'italic', 'underline', 'strike'],
                                            [{ 'header': 1 }, { 'header': 2 }],
                                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                            [{ 'script': 'sub' }, { 'script': 'super' }],
                                            [{ 'indent': '-1' }, { 'indent': '+1' }],
                                            [{ 'size': ['small', false, 'large', 'huge'] }],
                                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                            [{ 'color': [] }, { 'background': [] }],
                                            [{ 'align': [] }],
                                            ['clean']
                                        ]
                                    }
                                });
                                quill.clipboard.dangerouslyPasteHTML('{{$news->description}}');
                                quill.on('text-change', function () {
                                    content = quill.root.innerHTML;
                                });
                            })();
                        ">
                            <div x-ref="editor" style="min-height: 200px;"></div>
                            <textarea name="description" id="description" x-model="content" style="display: none;" required="true"></textarea>
                            <div id="description-error" class="text-danger mt-1" style="display: none;"></div>
                            <div class="col-md-12">
                                @error('description')
                                    <strong>{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="form-group row" x-data="{ imagePreview: '{{ $news->thumbURL() }}', updatePreview(event) { 
                    const file = event.target.files[0]; 
                    const reader = new FileReader(); 
                    reader.onload = (e) => { this.imagePreview = e.target.result; }; 
                    if (file) reader.readAsDataURL(file); 
                } }">
                {!! Form::label('image', 'Image', ['class' => 'col-md-2 col-form-label']) !!}
                <div class="col-md-10">
                    <div>
                        {!! Form::file('image', ['accept' => 'image/*', 'x-on:change' => 'updatePreview($event)']) !!}
                        @error('image')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                    <img class="mt-3" x-bind:src="imagePreview" alt="Image preview" style="max-width: 150px; max-height: 150px; object-fit: cover;" />
                </div>
                </div>


                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled*', ['class' => 'col-md-2 form-check-label']) !!}
                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="checkbox" id="checkEnable" name="enabled" value="1" class="form-check-input" {{ $news->enabled ? 'checked' : '' }} />
                        <label class="form-check-label" for="checkEnable">Visibility</label>
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
                {!! Form::submit('Update', ['class' => 'btn btn-primary float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>
@endsection
