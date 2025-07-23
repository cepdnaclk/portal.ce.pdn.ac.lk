<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a href="{{ route('frontend.index') }}" class="navbar-brand">{{ appName() }}</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="@lang('Toggle navigation')">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                @if (config('boilerplate.locale.status') && count(config('boilerplate.locale.languages')) > 1)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLanguageLink"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __(getLocaleName(app()->getLocale())) }}
                        </a>
                        @include('includes.partials.lang')
                    </li>
                @endif

                @guest
                    <li class="nav-item">
                        <a href="{{ route('frontend.auth.login') }}"
                            class="nav-link {{ activeClass(Route::is('frontend.auth.login')) }}">
                            @lang('Login')
                        </a>
                    </li>

                    @if (config('boilerplate.access.user.registration'))
                        <li class="nav-item">
                            <a href="{{ route('frontend.auth.register') }}"
                                class="nav-link {{ activeClass(Route::is('frontend.auth.register')) }}">
                                @lang('Register')
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="rounded-circle" style="max-height: 20px" src="{{ $logged_in_user->avatar }}"
                                alt="Avatar" />
                            {{ $logged_in_user->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if ($logged_in_user->isAdmin())
                                <li>
                                    <a href="{{ route('dashboard.home') }}" class="dropdown-item">
                                        @lang('Dashboard')
                                    </a>
                                </li>
                            @endif

                            @if ($logged_in_user->isUser())
                                <li>
                                    <a href="{{ route('intranet.user.index') }}"
                                        class="dropdown-item {{ activeClass(Route::is('intranet.user.index')) }}">
                                        @lang('Intranet')
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('intranet.user.account') }}"
                                    class="dropdown-item {{ activeClass(Route::is('intranet.user.account')) }}">
                                    @lang('Profile')
                                </a>
                            </li>

                            <li>
                                <a href="#" class="dropdown-item"
                                    onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    @lang('Logout')
                                </a>
                                <form id="logout-form" action="{{ route('frontend.auth.logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

@if (config('boilerplate.frontend_breadcrumbs'))
    @include('frontend.includes.partials.breadcrumbs')
@endif
