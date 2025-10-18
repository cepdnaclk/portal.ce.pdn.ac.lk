@extends('backend.layouts.app')

@section('title', 'View Taxonomy File')

@section('content')
    <div class="container mt-4">
        <x-backend.card>
            <x-slot name="header">
                Taxonomy File: {{ $taxonomyFile->file_name }}
            </x-slot>

            <x-slot name="body">
                <p>
                    <a class="btn btn-sm btn-outline-primary"
                        href="{{ route('download.taxonomy-file', [
                            'file_name' => $taxonomyFile->file_name,
                            'extension' => $taxonomyFile->getFileExtension(),
                        ]) }}"
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
                        <img src="{{ route('download.taxonomy-file', [
                            'file_name' => $taxonomyFile->file_name,
                            'extension' => $taxonomyFile->getFileExtension(),
                        ]) }}"
                            alt="Image Preview" class="img-fluid img-thumbnail"
                            style="max-width: 100%; height: auto; max-height: 240px;">
                    </div>
                @endif

                <!-- Basic details -->
                <div class="card">
                    <div class="card-body">
                        <div class="card-title" style="text-align: left; text-decoration: none;">Basic Info</div>
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
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
