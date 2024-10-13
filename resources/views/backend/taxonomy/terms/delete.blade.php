@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy Term'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy Term: Delete | {{ $taxonomyTerm->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $taxonomyTerm->name }}"</i></strong> ?
                </p>
                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('taxonomy.terms.destroy', ['taxonomy' => $taxonomy->id, 'term' => $taxonomyTerm->id]),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('taxonomy.terms.index', $taxonomy->id) }}" class="btn btn-light mr-2">Back</a>
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection

