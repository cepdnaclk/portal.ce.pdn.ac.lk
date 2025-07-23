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
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('Success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <livewire:backend.taxonomy-file-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
