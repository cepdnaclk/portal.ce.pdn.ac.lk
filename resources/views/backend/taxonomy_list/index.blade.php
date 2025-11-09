@extends('backend.layouts.app')

@section('title', __('Taxonomy Lists'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy Lists
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.list.editor'))
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.taxonomy-lists.create')" :text="__('Create a List')" />
                @endif
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissable="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.taxonomy-list-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
