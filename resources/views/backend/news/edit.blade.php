@extends('backend.layouts.app')

@section('title', __('News'))

@section('content')
    <div>
        {!! Form::open([
            'url' => route('dashboard.news.update', compact('newsItem')),
            'method' => 'put',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        <x-backend.card>
            <x-slot name="header">
                News : Edit | {{ $newsItem->id }}
            </x-slot>

            <x-slot name="body">
                <!-- Title -->
                <div class="form-group row">
                    {!! Form::label('message', 'Title*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('title', $newsItem->title, [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                        @error('title')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Type -->
                <div class="form-group row">
                    {!! Form::label('type', 'Type*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::select('type', $types, $newsItem->type, [
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => '',
                        ]) !!}
                        @error('type')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                    {!! Form::label('description', 'Description*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::textarea('message', $newsItem->description, [
                            'class' => 'form-control',
                            'rows' => 3,
                            'required' => true,
                        ]) !!}
                        @error('description')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- Enabled -->
                <div class="form-group row">
                    {!! Form::label('enabled', 'Enabled*', ['class' => 'col-md-2 form-check-label']) !!}

                    <div class="col-md-4 form-check">
                        <input type="checkbox" name="enabled" value="1" class="form-check-input0"
                            {{ $newsItem->enabled == 1 ? 'checked' : '' }} />
                        @error('enabled')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- link URL -->
                <div class="form-group row">
                    {!! Form::label('link_url', 'Link URL*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_url', $newsItem->link_url, [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                        @error('link_url')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

                <!-- link Caption -->
                <div class="form-group row">
                    {!! Form::label('link_caption', 'Link Caption*', ['class' => 'col-md-2 col-form-label']) !!}

                    <div class="col-md-10">
                        {!! Form::text('link_caption', $newsItem->link_caption, [
                            'class' => 'form-control',
                            'required' => true,
                        ]) !!}
                        @error('link_caption')
                            <strong>{{ $message }}</strong>
                        @enderror
                    </div>
                </div>

            </x-slot>
            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary float-right']) !!}
            </x-slot>

        </x-backend.card>
        {!! Form::close() !!}
    </div>
@endsection
