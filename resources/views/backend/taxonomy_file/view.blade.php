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
            <<<<<<< HEAD <span id="download-link">{{ route('download.taxonomy-files', $taxonomyFile->file_name) }}</span>
        </p>
        <p>
            <a class="btn btn-sm btn-outline-primary"
                href="{{ route('download.taxonomy-files', $taxonomyFile->file_name) }}"=======<span
                id="download-link">{{ route('download.taxonomy-files', [
                    'file_name' => $taxonomyFile->file_name,
                    'extension' => $taxonomyFile->getFileExtension(),
                ]) }}</span>
        </p>
        <p>
            <a class="btn btn-sm btn-outline-primary"
                href="{{ route('download.taxonomy-files', [
                    'file_name' => $taxonomyFile->file_name,
                    'extension' => $taxonomyFile->getFileExtension(),
                ]) }}">>>>>>>
                origin/release-3.2.0
                target="_blank">
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

        @if ($showPreview)
            <div class="mt-4 pb-4">
                <h5>Preview</h5>
                <img src="{{ route('download.taxonomy-files', [
                    'file_name' => $taxonomyFile->file_name,
                    'extension' => $taxonomyFile->getFileExtension(),
                ]) }}"
                    alt="Image Preview" class="img-fluid img-thumbnail"
                    style="max-width: 100%; height: auto; max-height: 240px;">
            </div>
        @endif

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
