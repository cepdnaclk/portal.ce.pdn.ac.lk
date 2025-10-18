@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy List'))

@section('content')

    <x-backend.card>
        <x-slot name="header">
            Delete : Taxonomy List | {{ $taxonomyList->name }}
        </x-slot>

        <x-slot name="body">
            <p>Are you sure you want to delete
                <strong><i>"{{ $taxonomyList->name }}"</i></strong> ?
            </p>

            @if (is_array($taxonomyList->items) && count($taxonomyList->items))
                <x-utils.alert type="warning">
                    {{ __('The list currently contains :count items. This action cannot be done.', ['count' => count($taxonomyList->items)]) }}
                </x-utils.alert>
            @endif

        </x-slot>

        <x-slot name="footer">
            {!! Form::open([
                'url' => route('dashboard.taxonomy-lists.destroy', compact('taxonomyList')),
                'method' => 'delete',
            ]) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger float-end btn-w-150']) !!}
            {!! Form::close() !!}

            <a href="{{ route('dashboard.taxonomy-lists.index') }}"
                class="btn btn-light btn-outline-secondary btn-w-150 float-end mr-2">Back</a>
        </x-slot>
    </x-backend.card>
@endsection
