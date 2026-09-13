@extends('backend.layouts.app')

@section('title', __('Profiles'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Profiles
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.access.profiles.editor'))
                    <x-utils.link icon="fa fa-compress" class="card-header-action" :href="route('dashboard.profiles.merge.select')" :text="__('Merge Profiles')" />
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.profiles.create')" :text="__('Create a Profile')" />
                @endif
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissable="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.profiles-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
