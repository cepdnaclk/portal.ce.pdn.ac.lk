@extends('backend.layouts.app')

@section('title', __('Create Event'))

@section('content')
    <div>
        {!! Form::open([
            'url' => route('dashboard.event.store'),
            'method' => 'post',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                Event : Create
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

                <!-- Published At -->
                <div class="form-group row">
                    {!! Form::label('published_at', 'Publish at*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-3">
                        {!! Form::date('published_at', date('Y-m-d'), ['class' => 'form-control']) !!}
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
                            <span class="me-2" id="url_hint">https://www.ce.pdn.ac.lk/events/{yyyy-mm-dd}-</span>
                            <span class="flex-grow-1"> {!! Form::text('url', '', ['class' => 'form-control']) !!}</span>
                        </div>
                        @error('url')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Event Type (Dropdown with Checkboxes) -->
                <div class="form-group row">
                    {!! Form::label('event_type', 'Event Type*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <x-backend.dropdown_checkbox :options-map="\App\Domains\Event\Models\Event::eventTypeMap()">
                            <x-backend.taxonomy_tooltip
                                edit-url="{{ route('dashboard.taxonomy.alias', ['code' => 'events']) }}" placement="auto">
                            </x-backend.taxonomy_tooltip>
                        </x-backend.dropdown_checkbox>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <livewire:backend.richtext-editor-component name="description" value="" />
                        <div class="col-md-12">
                            @error('description')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="form-group row" x-data="{
                    imagePreview: null,
                    updatePreview(event) {
                        const file = event.target.files[0];
                        const reader = new FileReader();
                        reader.onload = (e) => { this.imagePreview = e.target.result; };
                        if (file) reader.readAsDataURL(file);
                    }
                }">
                    {!! Form::label('image', 'Image', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::file('image', ['accept' => 'image/*', 'x-on:change' => 'updatePreview($event)']) !!}
                        @error('image')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror

                        <div x-show="imagePreview" style="margin-top: 10px;">
                            <img x-bind:src="imagePreview" alt="Image Preview"
                                style="max-width: 200px; max-height: 200px; object-fit: cover;" />
                        </div>
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled', ['class' => 'col-md-2 form-check-label']) !!}

                    <div class="col-md-4 form-check form-switch mx-4">
                        <input type="checkbox" id="checkEnable" name="enabled" value="1"
                            class="form-check-input checkbox-lg" checked />
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
                        {!! Form::text('link_url', '', ['class' => 'form-control']) !!}
                        @error('link_url')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Link Caption -->
                <div class="form-group row">
                    {!! Form::label('link_caption', 'Link Caption', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_caption', '', ['class' => 'form-control']) !!}
                        @error('link_caption')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Start at -->
                <div class="form-group row">
                    {!! Form::label('start_at', 'Start At*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-4">
                        {!! Form::datetimeLocal('start_at', '', ['class' => 'form-control']) !!}
                        @error('start_at')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        (If it is a whole day event, set the time as 12:00 AM)
                    </div>
                </div>

                <!-- End at -->
                <div class="form-group row">
                    {!! Form::label('end_at', 'End At', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-4">
                        {!! Form::datetimeLocal('end_at', '', ['class' => 'form-control']) !!}
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
                    {!! Form::label('location', 'Location*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('location', '', ['class' => 'form-control']) !!}
                        @error('location')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary btn-w-150 float-end', 'id' => 'submit-button']) !!}
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
