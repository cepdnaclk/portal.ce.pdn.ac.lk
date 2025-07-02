<textarea name="{{ $name }}" id="{{ $name }}" rows="10" class="form-control">{!! $value !!}</textarea>

@push('after-scripts')
    <script>
        const {
            ClassicEditor,
            Essentials,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Subscript,
            Superscript,
            Font,
            Paragraph,
            Heading,
            Indent,
            IndentBlock,
            Link,
            List
        } = CKEDITOR;

        // WYSIWYG Editor
        ClassicEditor
            .create(document.querySelector('#{{ $name }}'), {
                licenseKey: '{{ env('CKEDITOR_LICENSE_KEY') }}',
                plugins: [
                    Essentials,
                    Bold,
                    Italic,
                    Underline,
                    Strikethrough,
                    Subscript,
                    Superscript,
                    Font,
                    Paragraph,
                    Heading,
                    Indent,
                    IndentBlock,
                    Link,
                    List
                ],
                toolbar: [
                    'undo', 'redo', '|',
                    'heading', '|',
                    'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor', '|',
                    'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code', '|',
                    'link', '|',
                    'bulletedList', 'numberedList', 'blockQuote', '|',
                    'outdent', 'indent', '|',
                ],
                shouldNotGroupWhenFull: true
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
