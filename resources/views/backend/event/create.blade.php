@extends('backend.layouts.app')

@section('title', __('Event'))

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
                        {!! Form::text('title', '', ['class' => 'form-control', 'required' => true]) !!}
                        @error('title')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Type -->



                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        <div id="editor-container" style="height: auto;"></div>
                        <textarea name="description" id="description" style="display:none;"></textarea>
                        @error('description')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Image -->
                <div class="form-group row">
                    {!! Form::label('image', 'Image*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::file('image', ['class' => 'form-control']) !!}
                        @error('image')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled*', ['class' => 'col-md-2 form-check-label']) !!}

                    <div class="col-md-4 form-check">
                        {!! Form::checkbox('enabled', '1', ['class' => 'form-check-input', 'required' => true]) !!}
                        @error('enabled')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- link URL -->
                <div class="form-group row">
                    {!! Form::label('link_url', 'Link URL*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_url', '', ['class' => 'form-control', 'required' => true]) !!}
                        @error('link_url')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- link Caption -->
                <div class="form-group row">
                    {!! Form::label('link_caption', 'Link Caption*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_caption', '', ['class' => 'form-control', 'required' => true]) !!}
                        @error('link_caption')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- start time -->
                <div class="form-group row">
                    {!! Form::label('start_at', 'Start Time*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-3">
                        {!! Form::text('start_at', '', ['class' => 'form-control', 'id' => 'start_at', 'required' => true]) !!}
                        @error('start_at')                                
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- end time -->
                <div class="form-group row">
                    {!! Form::label('end_at', 'End Time*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-3">
                        {!! Form::text('end_at', '', ['class' => 'form-control', 'id' => 'end_at', 'required' => true]) !!}
                        @error('end_at')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- location -->
                <div class="form-group row">
                    {!! Form::label('location', 'Location*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('location', '', ['class' => 'form-control', 'required' => true]) !!}
                        @error('location')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <script>
                    // Initialize flatpickr on the input field
                    flatpickr("#start_at", {
                        altInput: true,
                        enableTime: true,        // Enable time selection
                        dateFormat: "Y-m-d H:i", // Set the date and time format
                    });
                    flatpickr("#end_at", {
                        altInput: true,
                        enableTime: true,        // Enable time selection
                        dateFormat: "Y-m-d H:i", // Set the date and time format
                    });
                </script>

            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>

        {!! Form::close() !!}
    </div>
@endsection
