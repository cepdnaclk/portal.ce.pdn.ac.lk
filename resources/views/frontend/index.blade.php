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
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <style>
        html,
        body {
            background-color:#f8fafc;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
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

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
            
        }

        

        .btn-primary{
            border-radius: 50px;
        }

        .btn-primary:hover{
            background-color: black;
        }

        .department-name {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
            color: white;
            font-size: 0.9rem
        }
        .department-name small {
            display: block;
            font-size: 0.8rem
        }
        .navbar-brand img {
            vertical-align: middle;
        }
        @media (max-width: 576px) {
            .department-name {
                font-size: 0.8rem;
            }
            .department-name small {
                font-size: 0.7rem;
            }
            .login-button{
                font-size: 10px;
            }
        }
        @media (max-width: 430px) {
            .department-name {
                font-size: 0.6rem;
            }
            .department-name small {
                font-size: 0.5rem;
            }
            .login-button{
                font-size: 0.5rem;
            }
        }

        .login-button {
        background-color: gray;
        color: white;
        /* font-size: 14px; */
        padding: 8px 28px;
        border-radius: 15px;
        text-decoration: none;
        transition: 0.3s background-color;
        }

        .login-button:hover{
            background-color:#EBA603;
            text-decoration: none;
        }

        .navbar-toggler{
            border: none;
            font-size: 1.25rem;

        }
        .navbar-toggler:focus, .btn-close:focus{
            box-shadow: none;
            outline: none;

        }


        .nav-link{
            color: gray !important;
            font-weight: 500;
            position: relative;
        }

        .nav-link:hover, .nav-link.active{
            color: white !important;
        }

        @media (min-width: 991px){
            .nav-link::before{
                content: "";
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translate(-50%);
                width: 100%;
                height: 2px;
                background-color: #EBA603;
                visibility: hidden;
                transition: 0.5s ease-in-out;

            }

            .nav-link:hover::before, .nav-link.active::before{
                
                width: 100%;
                visibility: visible;
            }

        }


        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255, 255, 255, 1%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* Optional: Adjust the gap as needed */
        }


    </style>
    @stack('after-styles')
</head>

<body>
    @include('includes.partials.read-only')
    @include('includes.partials.logged-in-as')
    @include('includes.partials.announcements')


    <!-- Navigation  -->
    <nav class="navbar navbar-expand-lg fixed-top bg-black ">
        <div class="container-fluid" style="height: 40px;">
          <a class="navbar-brand me-auto" href="#">
            <img src="https://github.com/cepdnaclk/cepdnaclk.github.io/blob/master/assets/images/crest.png?raw=true" alt="department-logo" style="max-height: 38px; width: auto;" />
            <div class="department-name">
              Department of Computer Engineering <br />
              <small class="small">University of Peradeniya, Sri Lanka</small>
            </div>
          </a>
          
          @auth
            <div
                class="offcanvas offcanvas-end"
                tabindex="-1"
                id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel"
            >
                <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
                    <img src="https://www.ce.pdn.ac.lk/assets/images/banner.svg" alt="" style="height: 45px" />
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas"
                    aria-label="Close"
                ></button>
                </div>
                <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                    <li class="nav-item">
                    <a class="nav-link active mx-lg-2" aria-current="page" href="#"
                        > HOME</a
                    >
                    </li>
                    <li class="nav-item">
                    <a class="nav-link  mx-lg-2" aria-current="page" href="{{ route('intranet.user.index') }}"
                        > INTRANET</a
                    >
                    </li>
                    @if ($logged_in_user->isAdmin())
                        <li class="nav-item">
                        <a class="nav-link mx-lg-2" aria-current="page" href="{{ route('dashboard.home') }}"
                            >DASHBOARD</a
                        >
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" aria-current="page" href="{{ route('intranet.user.account') }}"
                            > ACCOUNT</a>
                        </li>
                </ul>
            </div>
            </div>
        
          @else
          <div class="button-container">
            <a href="{{ route('frontend.auth.login') }}" class="login-button">Login</a>

            @if (config('boilerplate.access.user.registration'))
                    <a href="{{ route('frontend.auth.register') }}" class="login-button">Register</a>
            @endif

          </div>

          @endauth

          @auth
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
          @endauth
        </div>
      </nav>

    <div id="app" class="flex-center position-ref full-height">
        <div class="top-right links">
            {{-- @auth
                <a href="{{ route('intranet.user.index') }}">@lang('Intranet')</a>
                @if ($logged_in_user->isAdmin())
                    <a href="{{ route('dashboard.home') }}">@lang('Dashboard')</a>
                @endif
                <a href="{{ route('intranet.user.account') }}">@lang('Account')</a>
            @else
                <a href="{{ route('frontend.auth.login') }}">@lang('Login')</a>

                @if (config('boilerplate.access.user.registration'))
                    <a href="{{ route('frontend.auth.register') }}">@lang('Register')</a>
                @endif
            @endauth --}}
        </div><!--top-right-->

        <div class="content">
            @include('includes.partials.messages')

            <div class="title m-b-md">
                {{ config('app.name', 'Laravel') }}
            </div><!--title-->

            <div class="links">
                <a href="https://github.com/cepdnaclk/portal.ce.pdn.ac.lk" target="_blank"><i class="fab fa-github"></i>
                    GitHub</a>
            </div><!--links-->
        </div><!--content-->
    </div><!--app-->

    <footer class="mt-5 px-1 text-white bg-black" style="height: 40px">
        <div class="container-fluid d-flex justify-content-center">
          <p>
            Copyright &copy; 2024 Department of Computer Engineering - University
            of Peradeniya
          </p>
        </div>
      </footer>

    @stack('before-scripts')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/frontend.js') }}"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    @stack('after-scripts')
</body>

</html>
