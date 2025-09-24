@extends('backend.layouts.app')

@section('title', __('Dashboard'))

@section('content')
    {{-- Media Management --}}
    @if ($logged_in_user->hasAnyPermission(['user.access.editor.news', 'user.access.editor.events']))
        <x-backend.card>
            <x-slot name="header">
                @lang('Media')
            </x-slot>

            <x-slot name="body" style="min-height: 20vh;" class="container-fluid overflow-auto">
                <div class="row g-3">
                    {{-- Announcements --}}
                    @if ($logged_in_user->hasAllAccess())
                        <x-backend.shortcut-card route="{{ route('dashboard.announcements.index') }}" label="Announcements"
                            icon="fa-bullhorn" color="danger" />
                    @endif

                    {{-- News --}}
                    @if ($logged_in_user->hasPermissionTo('user.access.editor.news'))
                        <x-backend.shortcut-card route="{{ route('dashboard.news.index') }}" label="News"
                            icon="fa-newspaper-o" color="warn" />
                    @endif

                    {{-- Events --}}
                    @if ($logged_in_user->hasPermissionTo('user.access.editor.events'))
                        <x-backend.shortcut-card route="{{ route('dashboard.event.index') }}" label="Events"
                            icon="fa-calendar" color="info" />
                    @endif
                </div>
            </x-slot>
        </x-backend.card>
    @endif

    {{-- Academics --}}
    @if ($logged_in_user->hasAnyPermission(['user.access.academic.semester', 'user.access.academic.course']))
        <x-backend.card>
            <x-slot name="header">
                @lang('Academics')
            </x-slot>

            <x-slot name="body" style="min-height: 20vh;" class="container-fluid overflow-auto">
                <div class="row g-3">
                    {{-- Course + Curriculum --}}
                    <x-backend.shortcut-card
                        route="{{ route('dashboard.courses.index') }}?filters[academic_program]=undergraduate&filters[version]=1"
                        label="Curriculum Effective till E21" icon="fa-book" color="danger" />
                    <x-backend.shortcut-card
                        route="{{ route('dashboard.courses.index') }}?filters[academic_program]=undergraduate&filters[version]=2"
                        label="Curriculum Effective from E22" icon="fa-book" color="warning" />

                    {{-- Semesters --}}
                    <x-backend.shortcut-card route="{{ route('dashboard.semesters.index') }}" label="Semesters"
                        icon="fa-bullhorn" color="success" />
                </div>
            </x-slot>
        </x-backend.card>
    @endif

    {{-- Student/Staff Management --}}
    @if ($logged_in_user->hasAnyPermission(['user.access.taxonomy.data.editor', 'user.access.taxonomy.data.viewer']))
        <x-backend.card>
            <x-slot name="header">
                @lang('Student/Staff Management')
            </x-slot>

            <x-slot name="body" style="min-height: 20vh;" class="container-fluid overflow-auto">
                <div class="row g-3">
                    {{-- Students --}}
                    <x-backend.shortcut-card route="{{ route('dashboard.taxonomy.alias', ['code' => 'undergraduate']) }}"
                        label="Undergraduate Students" icon="fa-graduation-cap" color="danger" />
                    <x-backend.shortcut-card route="{{ route('dashboard.taxonomy.alias', ['code' => 'postgraduate']) }}"
                        label="Postgraduate Students" icon="fa-graduation-cap" color="success" />
                    <x-backend.shortcut-card route="{{ route('dashboard.taxonomy.alias', ['code' => 'alumni']) }}"
                        label="Alumni Students" icon="fa-graduation-cap" color="warning" />

                    {{-- Staff --}}
                    <x-backend.shortcut-card route="{{ route('dashboard.taxonomy.alias', ['code' => 'academic-staff']) }}"
                        label="Academic Staff" icon="fa-users" color="" />
                    <x-backend.shortcut-card
                        route="{{ route('dashboard.taxonomy.alias', ['code' => 'temporary-academic-staff']) }}"
                        label="Temporary Academic Staff" icon="fa-users" color="info" />
                    <x-backend.shortcut-card
                        route="{{ route('dashboard.taxonomy.alias', ['code' => 'academic-support-staff']) }}"
                        label="Academic Support Staff" icon="fa-users" color="secondary" />
                </div>
            </x-slot>
        </x-backend.card>
    @endif

    {{-- Administration --}}
    @if ($logged_in_user->hasAllAccess())
        <x-backend.card>
            <x-slot name="header">
                @lang('Administration')
            </x-slot>

            <x-slot name="body" style="min-height: 20vh;" class="container-fluid overflow-auto">
                <div class="row g-3">
                    <x-backend.shortcut-card route="{{ route('dashboard.auth.user.index') }}" label="Users"
                        icon="fa-users" color="primary" />
                    <x-backend.shortcut-card route="{{ route('dashboard.auth.role.index') }}" label="Roles"
                        icon="fa-address-card" color="info" />
                    <x-backend.shortcut-card route="{{ route('log-viewer::logs.list') }}" label="Logs" icon="fa-list"
                        color="secondary" />
                </div>
            </x-slot>
        </x-backend.card>
    @endif

@endsection
