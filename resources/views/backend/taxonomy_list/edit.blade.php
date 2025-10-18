@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy List'))

@section('content')
    {!! Form::model($taxonomyList, [
        'url' => route('dashboard.taxonomy-lists.update', $taxonomyList),
        'method' => 'PUT',
        'class' => 'container',
    ]) !!}

    @csrf

    <x-backend.card>
        <x-slot name="header">
            Taxonomy List : Edit
        </x-slot>

        <x-slot name="body">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title" style="text-align: left; text-decoration: none;">Basic Configurations</h5>

                    <div class="mb-3">
                        {!! Form::label('name', 'Name*', ['class' => 'form-label']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required' => true]) !!}
                        @error('name')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>

                    @isset($taxonomies)
                        <div class="mb-3">
                            {!! Form::label('taxonomy_id', 'Related Taxonomy (Optional)', ['class' => 'form-label']) !!}

                            @if (count($taxonomyList->items ?? []) > 0)
                                {!! Form::text('taxonomy_id', null, ['class' => 'form-control', 'readonly' => true]) !!}

                                <small class="text-muted">
                                    {{ __('Related Taxonomy cannot be changed while the list contains items.') }}
                                </small>
                            @else
                                {!! Form::select(
                                    'taxonomy_id',
                                    $taxonomies->pluck('name', 'id')->prepend(__('— none —'), ''),
                                    $taxonomyList->taxonomy_id,
                                    ['class' => 'form-select'],
                                ) !!}
                            @endif
                            @error('taxonomy_id')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                    @endisset

                    <div class="mb-3">
                        {!! Form::label('data_type', 'Data Type*', ['class' => 'form-label']) !!}

                        @if (count($taxonomyList->items ?? []) > 0)
                            {!! Form::text('data_type', null, ['class' => 'form-control', 'readonly' => true]) !!}

                            <small class="text-muted">
                                {{ __('Data type cannot be changed while the list contains items.') }}
                            </small>
                        @else
                            {!! Form::select('data_type', $dataTypes, $taxonomyList->data_type, [
                                'class' => 'form-select',
                                'required' => true,
                                'disabled' => count($taxonomyList->items ?? []) > 0 ? 'disabled' : null,
                            ]) !!}
                        @endif

                        @error('data_type')
                            <strong class="text-danger d-block">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
            <a href="{{ route('dashboard.taxonomy-lists.index') }}"
                class="btn btn-light btn-outline-secondary btn-w-150 float-end mr-2">Back</a>
        </x-slot>
    </x-backend.card>

    {!! Form::close() !!}
@endsection
