@extends('backend.layouts.app')

@section('title', __('Gallery Management'))

@section('content')

    <div class="container">
        <x-backend.card>
            <x-slot name="header">
                Gallery Management: {{ $imageable->title }}
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link class="btn btn-secondary btn-sm" :href="route('dashboard.' . $type . '.edit', $imageable)" :text="__('Back to Edit')" />
            </x-slot>

            <x-slot name="body">
                <!-- Upload Section -->
                @if ($stats['can_add_more'])
                    <div class="mb-4">
                        <h5>Upload Images</h5>
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="images">Select Images (At least 1, max
                                    {{ $stats['max_images'] - $stats['total_images'] }}
                                    more)</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple
                                    accept="image/jpeg" required>
                                <small class="form-text text-muted">
                                    Recommended to use <b>4:3 aspect ration</b> for the best view.
                                    Only JPEG images are allowed. Maximum size:
                                    <b>{{ config('gallery.max_file_size') / 1024 }}MB</b> per image.
                                    Minimum dimensions:
                                    <b>{{ config('gallery.min_width') }}x{{ config('gallery.min_height') }}</b>
                                    pixels.
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-upload"></i> Upload Images
                            </button>
                        </form>
                        <div id="uploadProgress" class="mt-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                            </div>
                        </div>
                        <div id="uploadStatus" class="mt-3"></div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        Maximum number of images ({{ $stats['max_images'] }}) reached. Please delete some images before
                        uploading more.
                    </div>
                @endif

                <hr>

                <!-- Gallery Grid -->
                <div class="mb-4">
                    <h5>Gallery Images ({{ $stats['total_images'] }}/{{ $stats['max_images'] }})</h5>
                    <div id="galleryContainer" class="row g-3">
                        @forelse($imageable->gallery as $image)
                            <div class="col-md-4 gallery-item" data-id="{{ $image->id }}"
                                data-order="{{ $image->order }}">
                                <div class="card h-100">
                                    <div class="card-img-top d-flex justify-content-center align-items-center border-bottom bg-light"
                                        style="height:150px; overflow:hidden;">
                                        <img class="img-fluid" style="max-height:100%; width:auto;"
                                            src="{{ $image->getSizeUrl('thumb') }}" alt="{{ $image->alt_text }}">
                                    </div>

                                    <div class="card-body">
                                        @if ($image->is_cover)
                                            <span class="badge bg-success mb-2">Cover Image</span>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-success set-cover-btn mb-2"
                                                data-id="{{ $image->id }}">
                                                Set as Cover
                                            </button>
                                        @endif

                                        <div class="form-group">
                                            <label>Alt Text</label>
                                            <input type="text" class="form-control form-control-sm image-alt"
                                                data-id="{{ $image->id }}" value="{{ $image->alt_text }}"
                                                placeholder="Alt text for accessibility">
                                        </div>

                                        <div class="form-group mt-2">
                                            <label>Caption</label>
                                            <textarea class="form-control form-control-sm image-caption" data-id="{{ $image->id }}" rows="2"
                                                placeholder="Image caption">{{ $image->caption }}</textarea>
                                        </div>

                                        <div class="form-group mt-2">
                                            <label>Credit</label>
                                            <input type="text" class="form-control form-control-sm image-credit"
                                                data-id="{{ $image->id }}" value="{{ $image->credit }}"
                                                placeholder="Photo credit">
                                        </div>

                                        <div class="mt-3 d-flex justify-content-between">
                                            <button type="button" class="btn btn-sm btn-primary save-metadata-btn"
                                                data-id="{{ $image->id }}">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-image-btn"
                                                data-id="{{ $image->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>

                                        <small class="text-muted d-block mt-2">
                                            {{ number_format($image->file_size / 1024, 2) }} KB |
                                            {{ $image->width }}x{{ $image->height }}px |
                                            <a href="{{ $image->getSizeUrl('original') ?? $image->getUrl() }}"
                                                class="text-decoration-none" target="_blank" rel="noopener">Open full</a>
                                        </small>
                                    </div>
                                    <div class="card-footer text-center bg-light" style="cursor: move;">
                                        <i class="fas fa-grip-vertical"></i> Drag to reorder
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No images in gallery yet. Upload some images to get started.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadForm = document.getElementById('uploadForm');
            const galleryContainer = document.getElementById('galleryContainer');
            const uploadRoute = "{{ route('dashboard.' . $type . '.gallery.upload', $imageable) }}";
            const reorderRoute = "{{ route('dashboard.' . $type . '.gallery.reorder', $imageable) }}";
            const updateRoute = "{{ route('dashboard.gallery.update', ':id') }}";
            const deleteRoute = "{{ route('dashboard.gallery.destroy', ':id') }}";
            const setCoverRoute = "{{ route('dashboard.' . $type . '.gallery.set-cover', [$imageable, ':id']) }}";

            // Upload form handler
            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(uploadForm);
                    const uploadBtn = document.getElementById('uploadBtn');
                    const uploadProgress = document.getElementById('uploadProgress');
                    const uploadStatus = document.getElementById('uploadStatus');

                    uploadBtn.disabled = true;
                    uploadProgress.style.display = 'block';
                    uploadStatus.innerHTML = '';

                    fetch(uploadRoute, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                uploadStatus.innerHTML =
                                    `<div class="alert alert-success">${data.message}</div>`;
                                setTimeout(() => location.reload(), 1500);
                            }
                        })
                        .catch(error => {
                            uploadStatus.innerHTML =
                                `<div class="alert alert-danger">Upload failed: ${error.message}</div>`;
                        })
                        .finally(() => {
                            uploadBtn.disabled = false;
                            uploadProgress.style.display = 'none';
                        });
                });
            }

            // Initialize Sortable for drag and drop reordering
            if (galleryContainer && galleryContainer.children.length > 0) {
                new Sortable(galleryContainer, {
                    animation: 150,
                    handle: '.card-footer',
                    onEnd: function(evt) {
                        const orderedIds = Array.from(galleryContainer.children)
                            .map(item => item.dataset.id)
                            .filter(id => id);

                        fetch(reorderRoute, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')
                                        .value
                                },
                                body: JSON.stringify({
                                    ordered_ids: orderedIds
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Reordered successfully');
                            })
                            .catch(error => {
                                console.error('Reorder failed:', error);
                                alert('Failed to reorder images');
                            });
                    }
                });
            }

            // Save metadata buttons
            document.querySelectorAll('.save-metadata-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const imageId = this.dataset.id;
                    const altText = document.querySelector(`.image-alt[data-id="${imageId}"]`)
                        .value;
                    const caption = document.querySelector(`.image-caption[data-id="${imageId}"]`)
                        .value;
                    const credit = document.querySelector(`.image-credit[data-id="${imageId}"]`)
                        .value;

                    fetch(updateRoute.replace(':id', imageId), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')
                                    .value
                            },
                            body: JSON.stringify({
                                alt_text: altText,
                                caption: caption,
                                credit: credit
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                        })
                        .catch(error => {
                            alert('Failed to update image');
                        });
                });
            });

            // Set cover buttons
            document.querySelectorAll('.set-cover-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const imageId = this.dataset.id;

                    if (confirm('Set this image as the cover?')) {
                        fetch(setCoverRoute.replace(':id', imageId), {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'input[name="_token"]').value
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                location.reload();
                            })
                            .catch(error => {
                                alert('Failed to set cover image');
                            });
                    }
                });
            });

            // Delete buttons
            document.querySelectorAll('.delete-image-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const imageId = this.dataset.id;

                    if (confirm('Are you sure you want to delete this image?')) {
                        fetch(deleteRoute.replace(':id', imageId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'input[name="_token"]').value
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                location.reload();
                            })
                            .catch(error => {
                                alert('Failed to delete image');
                            });
                    }
                });
            });
        });
    </script>
@endpush
