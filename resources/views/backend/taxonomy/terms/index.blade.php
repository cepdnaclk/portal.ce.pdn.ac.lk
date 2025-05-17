@extends('backend.layouts.app')

@section('title', __('Taxonomy Terms'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy Terms: {{ $taxonomy->name }}
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.taxonomy.data.editor'))
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.taxonomy.terms.create', $taxonomy)" :text="__('Create Term')">
                    </x-utils.link>

                    <x-utils.link icon="c-icon cil-pencil" class="card-header-action" :href="route('dashboard.taxonomy.edit', $taxonomy)" :text="__('Edit Taxonomy')">
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

                <livewire:backend.taxonomy-term-table :taxonomy="$taxonomy" />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
