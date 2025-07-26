@extends('backend.layouts.app')

@section('title', __('Taxonomy Files'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy Files') }}
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.file.editor'))
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.taxonomy-files.create')" :text="__('Upload File')" />
                @endif
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissable="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.taxonomy-file-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
