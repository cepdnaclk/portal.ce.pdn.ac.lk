@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy : Delete | {{ $taxonomy->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $taxonomy->name }}"</i></strong> ?
                </p>
                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.taxonomy.destroy', compact('taxonomy')),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('dashboard.taxonomy.index') }}" class="btn btn-light mr-2">Back</a>
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
