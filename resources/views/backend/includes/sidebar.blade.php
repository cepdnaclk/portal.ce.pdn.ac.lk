<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <x-utils.link class="c-sidebar-nav-link" :href="route('dashboard.home')" :active="activeClass(Route::is('dashboard.home'), 'c-active')"
                icon="c-sidebar-nav-icon cil-speedometer" :text="__('Dashboard')" />
        </li>

        @if (
            $logged_in_user->hasAllAccess() ||
                ($logged_in_user->can('admin.access.user.list') ||
                    $logged_in_user->can('admin.access.user.deactivate') ||
                    $logged_in_user->can('admin.access.user.reactivate') ||
                    $logged_in_user->can('admin.access.user.clear-session') ||
                    $logged_in_user->can('admin.access.user.impersonate') ||
                    $logged_in_user->can('admin.access.user.change-password')))
            <li class="c-sidebar-nav-title">@lang('System')</li>

            <li
                class="c-sidebar-nav-dropdown {{ activeClass(Route::is('dashboard.auth.user.*') || Route::is('dashboard.auth.role.*'), 'c-open c-show') }}">
                <x-utils.link href="#" icon="c-sidebar-nav-icon cil-user" class="c-sidebar-nav-dropdown-toggle"
                    :text="__('Access')" />

                <ul class="c-sidebar-nav-dropdown-items">
                    @if (
                        $logged_in_user->hasAllAccess() ||
                            ($logged_in_user->can('admin.access.user.list') ||
                                $logged_in_user->can('admin.access.user.deactivate') ||
                                $logged_in_user->can('admin.access.user.reactivate') ||
                                $logged_in_user->can('admin.access.user.clear-session') ||
                                $logged_in_user->can('admin.access.user.impersonate') ||
                                $logged_in_user->can('admin.access.user.change-password')))
                        <li class="c-sidebar-nav-item">
                            <x-utils.link :href="route('dashboard.auth.user.index')" class="c-sidebar-nav-link" :text="__('User Management')"
                                :active="activeClass(Route::is('dashboard.auth.user.*'), 'c-active')" />
                        </li>
                    @endif

                    @if ($logged_in_user->hasAllAccess())
                        <li class="c-sidebar-nav-item">
                            <x-utils.link :href="route('dashboard.auth.role.index')" class="c-sidebar-nav-link" :text="__('Role Management')"
                                :active="activeClass(Route::is('dashboard.auth.role.*'), 'c-active')" />
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if ($logged_in_user->hasAllAccess())
            <li class="c-sidebar-nav-dropdown">
                <x-utils.link href="#" icon="c-sidebar-nav-icon cil-list" class="c-sidebar-nav-dropdown-toggle"
                    :text="__('Logs')" />

                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item">
                        <x-utils.link :href="route('log-viewer::dashboard')" class="c-sidebar-nav-link" :text="__('Dashboard')" />
                    </li>
                    <li class="c-sidebar-nav-item">
                        <x-utils.link :href="route('log-viewer::logs.list')" class="c-sidebar-nav-link" :text="__('Logs')" />
                    </li>
                </ul>
            </li>
        @endif

        {{-- Announcements --}}
        @if ($logged_in_user->hasAllAccess())
            <li
                class="c-sidebar-nav-dropdown {{ activeClass(Route::is('dashboard.announcements.*'), 'c-open c-show') }}">
                <x-utils.link href="#" icon="c-sidebar-nav-icon cil-bullhorn"
                    class="c-sidebar-nav-dropdown-toggle" :text="__('Announcements')"></x-utils.link>

                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item">
                        <x-utils.link :href="route('dashboard.announcements.index')" class="c-sidebar-nav-link" :text="__('Manage')"
                            :active="activeClass(Route::is('dashboard.announcements.*'), 'c-active')"></x-utils.link>
                    </li>
                </ul>
            </li>
        @endif

        {{-- News and Events --}}
        @if ($logged_in_user->hasAnyPermission(['user.access.editor.news', 'user.access.editor.events']))
            <li
                class="c-sidebar-nav-dropdown {{ activeClass(Route::is('dashboard.news.*') || Route::is('dashboard.event.*'), 'c-open c-show') }}">
                <x-utils.link href="#" icon="c-sidebar-nav-icon cil-newspaper"
                    class="c-sidebar-nav-dropdown-toggle" :text="__('Content Management')"></x-utils.link>

                <ul class="c-sidebar-nav-dropdown-items">
                    @if ($logged_in_user->hasPermissionTo('user.access.editor.news'))
                        {{-- News --}}
                        <li class="c-sidebar-nav-item">
                            <x-utils.link :href="route('dashboard.news.index')" class="c-sidebar-nav-link" :text="__('News')"
                                :active="activeClass(Route::is('dashboard.news.*'), 'c-active')"></x-utils.link>
                        </li>
                    @endif
                    @if ($logged_in_user->hasPermissionTo('user.access.editor.events'))
                        {{-- Events --}}
                        <li class="c-sidebar-nav-item">
                            <x-utils.link :href="route('dashboard.event.index')" class="c-sidebar-nav-link" :text="__('Events')"
                                :active="activeClass(Route::is('dashboard.event.*'), 'c-active')"></x-utils.link>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        {{-- Academic Program --}}
        @if ($logged_in_user->hasAnyPermission(['user.access.academic.semester', 'user.access.academic.course']))
            <li
                class="c-sidebar-nav-dropdown {{ activeClass(Route::is('dashboard.semesters.*') || Route::is('dashboard.courses.*'), 'c-open c-show') }}">
                <x-utils.link href="#" icon="c-sidebar-nav-icon cil-book" class="c-sidebar-nav-dropdown-toggle"
                    :text="__('Academic Program')"></x-utils.link>

                <ul class="c-sidebar-nav-dropdown-items">
                    {{-- Semesters --}}
                    <li class="c-sidebar-nav-item">
                        <x-utils.link :href="route('dashboard.semesters.index')" class="c-sidebar-nav-link" :text="__('Semesters')"
                            :active="activeClass(Route::is('dashboard.semesters.*'), 'c-active')"></x-utils.link>
                    </li>
                    {{-- Courses --}}
                    <li class="c-sidebar-nav-item">
                        <x-utils.link :href="route('dashboard.courses.index')" class="c-sidebar-nav-link" :text="__('Courses')"
                            :active="activeClass(Route::is('dashboard.courses.*'), 'c-active')"></x-utils.link>
                    </li>
                </ul>
            </li>
        @endif

        {{-- Taxonomies --}}
        @if (
            $logged_in_user->hasAnyPermission([
                'user.access.taxonomy.data.editor',
                'user.access.taxonomy.data.viewer',
                'user.access.taxonomy.file.editor',
                'user.access.taxonomy.file.viewer',
                'user.access.taxonomy.page.editor',
                'user.access.taxonomy.page.viewer',
            ]))
            <li class="c-sidebar-nav-dropdown {{ activeClass(Route::is('dashboard.taxonomy'), 'c-open c-show') }}">
                <x-utils.link href="#" icon="c-sidebar-nav-icon cil-sitemap" class="c-sidebar-nav-dropdown-toggle"
                    :text="__('Taxonomies')"></x-utils.link>

                <ul class="c-sidebar-nav-dropdown-items">
                    {{-- Taxonomy Data --}}
                    @if ($logged_in_user->hasAnyPermission(['user.access.taxonomy.data.editor', 'user.access.taxonomy.data.viewer']))
                        <li class="c-sidebar-nav-item">
                            <x-utils.link :href="route('dashboard.taxonomy.index')" class="c-sidebar-nav-link" :text="__('Data')"
                                :active="activeClass(Route::is('dashboard.taxonomy.*'), 'c-active')"></x-utils.link>
                        </li>
                    @endif

                    {{-- Taxonomy File
                    @if ($logged_in_user->hasAnyPermission(['user.access.taxonomy.file.editor', 'user.access.taxonomy.file.viewer']))
                        <li class="c-sidebar-nav-item">
                            <x-utils.link :href="route('dashboard.taxonomy.files.index')" class="c-sidebar-nav-link" :text="__('Files')"
                                :active="activeClass(Route::is('dashboard.taxonomy.*'), 'c-active')"></x-utils.link>
                        </li>
                    @endif --}}

                    {{-- TODO Taxonomy Page
                    @if ($logged_in_user->hasAnyPermission(['user.access.taxonomy.page.editor', 'user.access.taxonomy.page.viewer']))
                        <li class="c-sidebar-nav-item">
                            <x-utils.link :href="route('dashboard.taxonomy.pages.index')" class="c-sidebar-nav-link" :text="__('Pages')"
                                :active="activeClass(Route::is('dashboard.taxonomy.*'), 'c-active')"></x-utils.link>
                        </li>
                    @endif --}}
                </ul>
            </li>
        @endif
    </ul>

    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
        data-class="c-sidebar-minimized"></button>
</div><!--sidebar-->
