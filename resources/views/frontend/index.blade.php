<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ appName() }}</title>
    <meta name="description" content="@yield('meta_description', appName())">
    <meta name="author" content="@yield('meta_author', 'Anthony Rappa')">
    @yield('meta')

    @stack('before-styles')
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ mix('css/frontend.css') }}" rel="stylesheet">
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .subtitle {
            font-size: 48px;
        }

        /* Medium screens (tablets) */
        @media (max-width: 1024px) {
            .title {
                font-size: 64px;
            }

            .subtitle {
                font-size: 36px;
            }
        }

        /* Small screens (mobile) */
        @media (max-width: 768px) {
            .title {
                font-size: 48px;
            }

            .subtitle {
                font-size: 28px;
            }
        }

        /* Extra small screens (smaller mobile devices) */
        @media (max-width: 480px) {
            .title {
                font-size: 36px;
            }

            .subtitle {
                font-size: 24px;
            }
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .mb-30 {
            margin-bottom: 30px;
        }
    </style>
    @stack('after-styles')
</head>

<body>
    @include('includes.partials.read-only')
    @include('includes.partials.logged-in-as')
    @include('includes.partials.announcements')

    <div id="app" class="flex-center position-ref full-height">
        <div class="top-right links">
            @auth
                <a href="{{ route('intranet.user.index') }}">@lang('Intranet')</a>
                <a href="{{ route('dashboard.home') }}">@lang('Dashboard')</a>
                <a href="{{ route('intranet.user.account') }}">@lang('Profile')</a>
            @else
                <a href="{{ route('frontend.auth.login') }}">@lang('Login')</a>

                @if (config('boilerplate.access.user.registration'))
                    <a href="{{ route('frontend.auth.register') }}">@lang('Register')</a>
                @endif
            @endauth
        </div>
        <div class="content">
            @include('includes.partials.messages')

            <div class="mb-30">
                <div class="title">
                    {{ config('app.name', 'Laravel') }}
                </div>
                <div class="subtitle">Department of Computer Engineering</div>
            </div>

            <hr>

            <div class="links">
                <a href="https://github.com/cepdnaclk/portal.ce.pdn.ac.lk" target="_blank"><i class="fab fa-github"></i>
                    GitHub</a>
                <a href="{{ route('frontend.pages.terms') }}"><i class="fa fa-list"></i>
                    Terms & Conditions</a>
                <a href="{{ route('frontend.pages.contributors') }}"><i class="fa fa-user"></i>
                    Contributors</a>
            </div>
        </div>
    </div>
    @stack('before-scripts')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/frontend.js') }}"></script>
    @stack('after-scripts')
</body>

</html>
