@extends('backend.layouts.app')

@section('title', __('Profile Management'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                @lang('Profile Management')
            </x-slot>

            <x-slot name="headerActions">
                @can('user.access.profiles.edit')
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.profiles.create')" :text="__('Create Profile')" />
                @endcan
            </x-slot>

            <x-slot name="body">
                <livewire:backend.profiles-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
