@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy File'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy File : Delete') }} | {{ $taxonomyFile->id }}
            </x-slot>

            <x-slot name="body">
                <p>
                    {{ __('Are you sure you want to delete') }}
                    <strong><i>"{{ $taxonomyFile->file_name }}"</i></strong>?
                </p>

                <p class="mb-3">
                    <span class="text-muted">{{ __('Taxonomy:') }}</span>
                    {{ $taxonomyFile->taxonomy?->name ?? '—' }} &nbsp;|&nbsp;
                    <span class="text-muted">{{ __('Size:') }}</span>
                    {{ number_format($taxonomyFile->size / 1024, 1) }} KB
                </p>

                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.taxonomy-files.destroy', $taxonomyFile),
                        'method' => 'delete',
                        'class' => 'container p-0',
                    ]) !!}
                    <a href="{{ route('dashboard.taxonomy-files.index') }}" class="btn btn-light mr-2">
                        {{ __('Back') }}
                    </a>

                    {!! Form::submit(__('Delete'), ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
