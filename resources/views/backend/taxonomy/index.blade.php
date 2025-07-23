@extends('backend.layouts.app')

@section('title', __('Taxonomy'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.data.editor'))
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.taxonomy.create')" :text="__('Create Taxonomy')">
                    </x-utils.link>
                @endif
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissible="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.taxonomy-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
