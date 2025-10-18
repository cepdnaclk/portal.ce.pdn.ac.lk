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

                <div class="mt-3 border p-1 pb-3">
                    {!! $taxonomyPage->html !!}
                </div>
            </x-slot>

            <!-- Footer -->
            <x-slot name="footer">
                {!! Form::open([
                    'url' => route('dashboard.taxonomy-pages.destroy', $taxonomyPage),
                    'method' => 'delete',
                ]) !!}

                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-w-150 float-end ']) !!}
                <a href="{{ route('dashboard.taxonomy-pages.index') }}"
                    class="btn btn-light btn-outline-secondary btn-w-150 float-end mr-2">Back</a>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
