@extends('backend.layouts.app')

@section('title', __('Edit Event'))

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
            'url' => route('dashboard.event.update', compact('event')),
            'method' => 'put',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                Event : Edit | {{ $event->title }}
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('message', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('title', $event->title, [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                    </div>
                    @error('title')
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>

                <!-- Published At -->
                <div class="form-group row">
                    {!! Form::label('published_at', 'Publish at*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-3">
                        {!! Form::date('published_at', $event->published_at, ['class' => 'form-control', 'required' => true]) !!}
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
                                id="url_hint">https://ce.pdn.ac.lk/events/{{ $event->published_at }}-&nbsp;</span>
                            <span class="flex-grow-1"> {!! Form::text('url', $event->url, ['class' => 'form-control', 'required' => true]) !!}</span>
                        </div>
                        @error('url')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Event Type (Dropdown with Checkboxes) -->
                <div class="form-group row">
                    {!! Form::label('event_type', 'Event Type*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-5">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #d3d3d3; color: #000;">
                                Select Event Type
                            </button>
                            <ul class="dropdown-menu w-100 p-3" aria-labelledby="dropdownMenuButton" style="max-height: 300px; overflow-y: auto;">
                                @foreach(\App\Domains\Event\Models\Event::eventTypeMap() as $key => $value)
                                    <li class="dropdown-item">
                                        <label class="form-check-label d-block w-100" for="event_type_{{ $key }}">
                                            <input class="form-check-input me-2 event-type-checkbox" type="checkbox" name="event_type[]" value="{{ $key }}" id="event_type_{{ $key }}" data-label="{{ $value }}"
                                                @if(is_array($event->event_type) && in_array($key, $event->event_type)) checked @endif> <!-- Populate based on existing data -->
                                            {{ $value }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @error('event_type')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>

                    <!-- Selected tags appear here -->
                    <div class="col-md-5">
                        <div id="selected-tags" class="mt-2"></div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const checkboxes = document.querySelectorAll('.event-type-checkbox');
                        const selectedTagsContainer = document.getElementById('selected-tags');

                        // Initialize selected tags on page load
                        updateTags();

                        checkboxes.forEach(function(checkbox) {
                            checkbox.addEventListener('change', function() {
                                updateTags();
                            });
                        });

                        function updateTags() {
                            selectedTagsContainer.innerHTML = ''; // Clear previous tags
                            checkboxes.forEach(function(checkbox) {
                                if (checkbox.checked) {
                                    const tagLabel = checkbox.getAttribute('data-label');
                                    const tagElement = document.createElement('span');
                                    tagElement.classList.add('badge', 'bg-primary', 'me-1', 'mb-1');
                                    tagElement.innerHTML = tagLabel + ' <i class="bi bi-x-circle ms-1" style="cursor:pointer;"></i>'; // 'x' icon for removal
                                    
                                    // Remove tag when clicked
                                    tagElement.querySelector('i').addEventListener('click', function() {
                                        checkbox.checked = false;
                                        updateTags();
                                    });

                                    selectedTagsContainer.appendChild(tagElement);
                                }
                            });
                        }
                    });
                </script>


                


                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <div x-data="{ content: '{{ $event->description }}' }" x-init="(() => {
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
                            quill.clipboard.dangerouslyPasteHTML('{{ $event->description }}');
                            quill.on('text-change', function() {
                                content = quill.root.innerHTML;
                            });
                        })();">
                            <div x-ref="editor" style="min-height: 200px;"></div>
                            <textarea name="description" id="description" x-model="content" style="display: none;"></textarea>
                            <div class="col-md-12">
                                @error('description')
                                    <strong>{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="form-group row" x-data="{
                    imagePreview: '{{ $event->thumbURL() }}',
                    updatePreview(event) {
                        const file = event.target.files[0];
                        const reader = new FileReader();
                        reader.onload = (e) => { this.imagePreview = e.target.result; };
                        if (file) reader.readAsDataURL(file);
                    }
                }">
                    {!! Form::label('image', 'Image', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <div>
                            {!! Form::file('image', ['accept' => 'image/*', 'x-on:change' => 'updatePreview($event)']) !!}
                            @error('image')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                        <img class="mt-3" x-bind:src="imagePreview" alt="Image preview"
                            style="max-width: 150px; max-height: 150px; object-fit: cover;" />
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled', ['class' => 'col-md-2 form-check-label']) !!}

                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="checkbox" id="checkEnable" name="enabled"
                            value={{ $event->enable ? 'checked' : '""' }} class="form-check-input checkbox-lg"
                            {{ $event->enabled == 1 ? 'checked' : '' }} />
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
                        {!! Form::text('link_url', $event->link_url, [
                            'class' => 'form-control',
                        ]) !!}
                        @error('link_url')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Link Caption -->
                <div class="form-group row">
                    {!! Form::label('link_caption', 'Link Caption', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_caption', $event->link_caption, [
                            'class' => 'form-control',
                        ]) !!}
                        @error('link_caption')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Start time -->
                <div class="form-group row">
                    {!! Form::label('start_at', 'Start Time*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-4">
                        {!! Form::datetimeLocal('start_at', $event->start_at, [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                        @error('start_at')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        (If it is a whole day event, set the time as 12:00 AM)
                    </div>
                </div>

                <!-- Dnd time -->
                <div class="form-group row">
                    {!! Form::label('end_at', 'End Time', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-4">
                        {!! Form::datetimeLocal('end_at', $event->end_at, [
                            'class' => 'form-control',
                        ]) !!}
                        @error('end_at')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        (If it is a whole day event, set the time as 12:00 AM)
                    </div>
                </div>

                <!-- Location -->
                <div class="form-group row">
                    {!! Form::label('location', 'Event Location*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('location', $event->location, [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                        @error('location')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-right']) !!}
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>

    <script>
        document.getElementById('published_at').addEventListener('change', function() {
            document.getElementById('url_hint').textContent =
                `https://www.ce.pdn.ac.lk/events/${this.value.toLowerCase() ?? 'yyyy-mm-dd'}-`;
        });
    </script>
@endsection
