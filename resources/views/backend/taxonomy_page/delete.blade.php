@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy Page'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy Page : Delete | {{ $taxonomyPage->id }}
            </x-slot>

            <x-slot name="body">
                <p>
                    Are you sure you want to delete <strong><i>"{{ $taxonomyPage->slug }}"</i></strong>?
                </p>

                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.taxonomy-pages.destroy', $taxonomyPage),
                        'method' => 'delete',
                        'class' => 'container p-0',
                    ]) !!}

                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    <a href="{{ route('dashboard.taxonomy-pages.index') }}" class="btn btn-light mr-2">
                        Back
                    </a>
                    {!! Form::close() !!}
                </div>

                <div class="container vh-75 mt-5">
                    <h4>Preview:</h4>
                    <div class="border p-3">
                        {!! $taxonomyPage->html !!}
                    </div>
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
