@extends('backend.layouts.app')

@section('title', __('Event'))

@push('after-styles')
    <!-- Include Quill library -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
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
                Event : Edit | {{ $event->id }}
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
                        <strong>{{ $message }}</strong>
                    @enderror
                </div>

                <!-- Published At -->
                <div class="form-group row">
                    {!! Form::label('published_at', 'Publish at*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-3">
                        {!! Form::date('published_at', $event->published_at, ['class' => 'form-control', 'required' => true]) !!}
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
                            <span class="me-2">https://ce.pdn.ac.lk/events/{{ $event->published_at }}-&nbsp;</span>
                            <span class="flex-grow-1"> {!! Form::text('url', $event->url, ['class' => 'form-control', 'required' => true]) !!}</span>
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
                        <div id="editor-container" style="height: auto;min-height: 200px;">{!! $event->description !!}</div>
                        <textarea name="description" id="description" style="display:none;" required="true"></textarea>
                        @error('description')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Image -->
                <div class="form-group row">
                    {!! Form::label('image', 'Image*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        <div>
                            <img src="{{ $event->thumbURL() }}" alt="Image preview" class="pb-2"
                                style="max-width: 150px; max-height: 150px;" />
                        </div>
                        {!! Form::file('image', ['accept' => '.jpeg,.png,.jpg,.gif,.svg']) !!}
                        @error('image')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled', ['class' => 'col-md-2 form-check-label']) !!}

                    <div class="col-md-4 form-check">
                        <input type="checkbox" name="enabled" value="1" class="form-check-input0"
                            {{ $event->enabled == 1 ? 'checked' : '' }} />
                        @error('enabled')
                            <strong>{{ $message }}</strong>
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
                            <strong>{{ $message }}</strong>
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
                            <strong>{{ $message }}</strong>
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
                            <strong>{{ $message }}</strong>
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
                            <strong>{{ $message }}</strong>
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
