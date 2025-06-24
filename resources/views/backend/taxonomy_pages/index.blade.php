@extends('backend.layouts.app')

@section('title', __('Taxonomy Pages'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy Pages') }}
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.taxonomy.data.editor'))
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.taxonomy-pages.create')" :text="__('Create Page')" />
                @endif
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('Success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <livewire:backend.taxonomy-pages-table />

            </x-slot>

        </x-backend.card>
    </div>
@endsection
