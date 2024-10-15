@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy Term'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy Term: Delete | {{ $term->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $term->name }}"</i></strong> ?
                </p>
                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.taxonomy.terms.destroy', ['taxonomy' => $taxonomy, 'term' => $term]),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('dashboard.taxonomy.terms.index', $taxonomy) }}" class="btn btn-light mr-2">Back</a>
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection

