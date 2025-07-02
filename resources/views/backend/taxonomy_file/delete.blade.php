@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy File'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy File : Delete | {{ $taxonomyFile->id }}
            </x-slot>

            <x-slot name="body">
                <p>
                    Are you sure you want to delete
                    <strong><i>"{{ $taxonomyFile->file_name }}"</i></strong>?
                </p>

                @if ($showPreview)
                    <div class="mt-4 pb-4">
                        <img src="{{ route('download.taxonomy-file', [
                            'file_name' => $taxonomyFile->file_name,
                            'extension' => $taxonomyFile->getFileExtension(),
                        ]) }}"
                            alt="Image Preview" class="img-fluid img-thumbnail"
                            style="max-width: 100%; height: auto; max-height: 240px;">
                    </div>
                @endif

                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.taxonomy-files.destroy', $taxonomyFile),
                        'method' => 'delete',
                        'class' => 'container p-0',
                    ]) !!}
                    <a href="{{ route('dashboard.taxonomy-files.index') }}" class="btn btn-light mr-2">
                        Back
                    </a>

                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
