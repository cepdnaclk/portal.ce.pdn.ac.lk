<textarea name="{{ $name }}" id="{{ $name }}" rows="10" class="form-control">{!! $value !!}</textarea>

@push('after-scripts')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        const uploadUrl = @json($uploadUrl);
        const contentImagesInput = @json($contentImagesInput);

        const appendContentImage = (image) => {
            if (!contentImagesInput) {
                return;
            }

            const input = document.getElementById(contentImagesInput);
            if (!input) {
                return;
            }

            let images = [];
            try {
                images = JSON.parse(input.value || '[]');
                if (!Array.isArray(images)) {
                    images = [];
                }
            } catch (error) {
                images = [];
            }

            if (!images.find((item) => item.id === image.id)) {
                images.push(image);
                input.value = JSON.stringify(images);
            }
        };

        tinymce.init({
            selector: 'textarea#{{ $name }}',
            plugins: uploadUrl ? 'code table lists link preview image' : 'code table lists link preview',
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
                    items: uploadUrl ? ['table', 'hr', 'removeformat', 'code', 'preview', 'image'] : ['table',
                        'hr', 'removeformat', 'code', 'preview'
                    ]
                }
            ],
            toolbar_mode: 'sliding',
            images_file_types: [{}],
            file_picker_types: 'image',

            // URL management
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,

            // Image Upload handler
            images_upload_handler: uploadUrl ?
                (blobInfo) => new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('image', blobInfo.blob(), blobInfo.filename());

                    const tenantInput = document.getElementById('tenant_id');
                    if (tenantInput) {
                        formData.append('tenant_id', tenantInput.value);
                    }

                    fetch(uploadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData,
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (!data || !data.location) {
                                reject('Upload failed');
                                return;
                            }

                            appendContentImage({
                                id: data.id,
                                url: data.location,
                                path: data.path,
                                disk: data.disk ?? 'public',
                            });

                            resolve(data.location);
                        })
                        .catch((ex) => {
                            console.error('Upload error', ex);
                            reject('Upload failed');
                        });
                }) : undefined
        });
    </script>
@endpush
