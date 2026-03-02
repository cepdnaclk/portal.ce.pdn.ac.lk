@extends('backend.layouts.app')

@section('title', __('Edit Event'))

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

                <!-- Author -->
                <div class="form-group row">
                    {!! Form::label('author_id', 'Author*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        {!! Form::select('author_id', $authorOptions, $event->author_id ?? $event->created_by, [
                            'class' => 'form-select',
                            'required' => true,
                            'placeholder' => '',
                        ]) !!}
                        @error('author_id')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>
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
                            <span class="me-2" id="url_hint">/events/{{ $event->published_at }}-&nbsp;</span>
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
                    <div class="col-md-10">
                        <x-backend.dropdown-checkbox :selected="$event->event_type ?? []" :options-map="\App\Domains\ContentManagement\Models\Event::eventTypeMap()">
                            <x-backend.taxonomy-tooltip
                                edit-url="{{ route('dashboard.taxonomy.term.alias', ['code' => 'events']) }}"
                                placement="auto">
                            </x-backend.taxonomy-tooltip>
                        </x-backend.dropdown-checkbox>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}
                    <div class="col-md-10">
                        <livewire:backend.richtext-editor-component name="description"
                            value="{{ old('description', $event->description ?? '') }}" />
                        <div class="col-md-12">
                            @error('description')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </div>
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

                <!-- End time -->
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
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-end ms-2', 'id' => 'submit-button']) !!}
                @if (config('gallery.enabled'))
                    <a href="{{ route('dashboard.event.gallery.index', $event) }}"
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
                `/events/${this.value.toLowerCase() ?? 'yyyy-mm-dd'}-`;
        });
    </script>
@endsection
