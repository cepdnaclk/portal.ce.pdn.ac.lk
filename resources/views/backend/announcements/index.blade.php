@extends('backend.layouts.app')

@section('title', __('Announcements'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Announcements
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.announcements.create')" :text="__('Create Announcement')">
                </x-utils.link>
            </x-slot>

            <x-slot name="body">

                @if (session('Success'))
                    <x-utils.alert type="success" dismissible="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.announcement-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
