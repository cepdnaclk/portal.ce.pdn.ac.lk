<!doctype html>
<html lang="{{ htmlLang() }}" @langrtl dir="rtl" @endlangrtl>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ appName() }} | @yield('title')</title>
    <meta name="description" content="@yield('meta_description', appName())">
    <meta name="author" content="@yield('meta_author', 'cepdnaclk')">
    <link rel="icon" href="/assets/img/favicon.png">
    @yield('meta')

    @stack('before-styles')
    <link href="{{ mix('css/backend.css') }}" rel="stylesheet">
    <link href="{{ asset('css/multi-form.css')}}" rel="stylesheet"/>
    <livewire:styles />
    @stack('after-styles')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
</head>

<body class="c-app">
    @include('backend.includes.sidebar')

    <div class="c-wrapper c-fixed-components">
        @include('backend.includes.header')
        @include('includes.partials.read-only')
        @include('includes.partials.logged-in-as')
        @include('includes.partials.announcements')

        <div class="c-body">
            <main class="c-main">
                <div class="container-fluid">
                    <div class="fade-in">
                        @include('includes.partials.messages')
                        @yield('content')
                    </div><!--fade-in-->
                </div><!--container-fluid-->
            </main>
        </div><!--c-body-->

        @include('backend.includes.footer')
    </div><!--c-wrapper-->

    @stack('before-scripts')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/backend.js') }}"></script>
    <livewire:scripts />
    @stack('after-scripts')

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'], // toggled buttons

            [{
                'header': 1
            }, {
                'header': 2
            }], // custom button values
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'script': 'sub'
            }, {
                'script': 'super'
            }], // superscript/subscript
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }], // outdent/indent

            [{
                'size': ['small', false, 'large', 'huge']
            }], // custom dropdown
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],

            [{
                'color': []
            }, {
                'background': []
            }], // dropdown with defaults from theme
            [{
                'align': []
            }],

            ['clean'] // remove formatting button
        ];

        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            }
        });

        

    // Event listener for form submission
    document.getElementById('submit-button').addEventListener('click', function(data) {
        // Get Quill content
        var quillContent = quill.root.innerHTML;

        // Reference to the error message div and the editor container
        var descriptionError = document.getElementById('description-error');
        var editorContainer = document.getElementById('editor-container');

        // Check if the content is just the empty paragraph or is empty
        if (quillContent === '<p><br></p>' || quillContent.trim() === '') {
            
            data.preventDefault();

            // Show custom error message
            descriptionError.innerHTML = 'Description is required';
            descriptionError.style.display = 'block';

            // Highlight the Quill editor by changing its border color
            editorContainer.style.border = '2px solid red';

            return; 
        }

        

        // Populate hidden form field with Quill data
        var description = document.querySelector('textarea[name=description]');
        description.value = quillContent;
    });

    
    quill.on('text-change', function() {
        var descriptionError = document.getElementById('description-error');
        var editorContainer = document.getElementById('editor-container');

        descriptionError.style.display = 'none';
        editorContainer.style.border = '1px solid #ced4da'; 
    });



    </script>
</body>

</html>
