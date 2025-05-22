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
                    <div class="alert alert-success">
                        {{ session('Success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <livewire:backend.taxonomy-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
