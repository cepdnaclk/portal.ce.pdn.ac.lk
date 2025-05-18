@extends('backend.layouts.app')

@section('title', 'View Taxonomy File')

@section('content')
    <div class="container mt-4">
        <!-- Heading -->
        <h3>
            Taxonomy File:
            {{ $taxonomyFile->file_name }}
        </h3>

        <!-- Download button -->

        <p>
            <span id="download-link">{{ route('dashboard.taxonomy-files.download', $taxonomyFile->file_name) }}</span>
        </p>
        <p>
            <a class="btn btn-sm btn-outline-primary"
                href="{{ route('dashboard.taxonomy-files.download', $taxonomyFile->file_name) }}" target="_blank">
                <i class="fa fa-download"></i> Download
            </a>

            <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard()">Copy Link</button>

            <script>
                function copyToClipboard() {
                    const link = document.getElementById('download-link').textContent;
                    navigator.clipboard.writeText(link).then(() => {
                        alert('Link copied to clipboard!');
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                }
            </script>
        </p>

        <!-- Basic details -->
        <h5>Basic Info</h5>
        <ul>
            <li>
                <strong>Taxonomy:</strong>
                {{ $taxonomyFile->taxonomy?->name ?? '—' }}
            </li>
            <li>
                <strong>Uploaded At:</strong>
                {{ $taxonomyFile->created_at }}
            </li>
            @if (!empty($taxonomyFile->metadata['file_size']))
                <li>
                    <strong>File Size:</strong>
                    {{ $taxonomyFile->getFileSize() }}
                </li>
            @endif
            <li>
                <strong>Uploaded By:</strong>
                {{ $taxonomyFile->user_created->name ?? 'N/A' }}
            </li>
        </ul>

    </div>
@endsection
