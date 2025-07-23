@extends('backend.layouts.app')

@section('title', __('Taxonomy Pages'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy Pages') }}
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.page.editor'))
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.taxonomy-pages.create')" :text="__('Create a Page')" />
                @endif
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissible="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.taxonomy-page-table />

            </x-slot>

        </x-backend.card>
    </div>
@endsection
