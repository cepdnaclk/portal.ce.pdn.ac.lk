<textarea name="{{ $name }}" id="{{ $name }}" rows="10" class="form-control">{!! $value !!}</textarea>

@push('after-scripts')
    <script>
        // WYSIWYG Editor
        ClassicEditor
            .create(document.querySelector('#{{ $name }}'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'imageUpload', '|',
                    'bulletedList', 'numberedList', 'blockQuote', '|',
                    'undo', 'redo', 'fullscreen'
                ],
                ckfinder: {
                    uploadUrl: '{{ route('dashboard.taxonomy-pages.upload-image') }}?&_token={{ csrf_token() }}'
                }
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
