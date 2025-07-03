<textarea name="{{ $name }}" id="{{ $name }}" rows="10" class="form-control">{!! $value !!}</textarea>

@push('after-scripts')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#{{ $name }}',
            plugins: 'code table lists link preview',
            license_key: 'gpl',
            menubar: false,
            height: 500,
            branding: false,
            toolbar: [{
                    name: 'history',
                    items: ['undo', 'redo']
                },
                {
                    name: 'styles',
                    items: ['styles']
                },
                {
                    name: 'formatting',
                    items: ['bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript']
                },
                {
                    name: 'links',
                    items: ['link']
                },
                {
                    name: 'alignment',
                    items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify']
                },
                {
                    name: 'lists',
                    items: ['bullist', 'numlist']
                },
                {
                    name: 'indentation',
                    items: ['outdent', 'indent']
                },
                {
                    name: "tools",
                    items: ['table', 'hr', 'removeformat', 'code', 'preview']
                }
            ],
            toolbar_mode: 'sliding'
        });
    </script>
@endpush
