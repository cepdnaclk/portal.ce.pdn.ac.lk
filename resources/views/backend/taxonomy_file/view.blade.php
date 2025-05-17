@extends('backend.layouts.app')

@section('title', __('View Taxonomy File'))

@section('content')
    <div class="container mt-4">
        <!-- Heading -->
        <h3>
            {{ __('Taxonomy File:') }}
            {{ $taxonomyFile->file_name }}
        </h3>

        <!-- Download button -->
        <p>
            <a class="btn btn-sm btn-outline-primary" href="{{ route('dashboard.taxonomy-files.download', $taxonomyFile) }}"
                target="_blank">
                <i class="fa fa-download"></i> {{ __('Download') }}
            </a>
        </p>

        <!-- Basic details -->
        <h5>{{ __('Basic Info') }}</h5>
        <ul>
            <li>
                <strong>{{ __('Taxonomy') }}:</strong>
                {{ $taxonomyFile->taxonomy?->name ?? '—' }}
            </li>
            <li>
                <strong>{{ __('Uploaded At') }}:</strong>
                {{ $taxonomyFile->created_at }}
            </li>
            <li>
                <strong>{{ __('Uploaded By') }}:</strong>
                {{ $row->user_created->name ?? 'N/A' }}
            </li>
        </ul>

        <!-- Metadata -->
        @if (!empty($taxonomyFile->metadata))
            <h5>{{ __('Metadata') }}</h5>
            <pre class="p-3 border rounded">
{{ json_encode($taxonomyFile->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
            </pre>
        @endif
    </div>
@endsection
