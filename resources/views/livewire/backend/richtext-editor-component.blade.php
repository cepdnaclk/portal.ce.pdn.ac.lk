<textarea name="{{ $name }}" id="{{ $name }}" rows="10" class="form-control">{!! $value !!}</textarea>

@push('after-scripts')
    <script>
        // WYSIWYG Editor
        ClassicEditor
            .create(document.querySelector('#{{ $name }}'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', 'blockQuote', '|',
                    'undo', 'redo',
                ],
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
