@extends('backend.layouts.app')

@section('title', __('Taxonomy Terms'))

@section('content')
    <div>
        @if ($taxonomy && $taxonomy->description)
            <livewire:backend.expandable-info-card :title="'Taxonomy: ' . $taxonomy->name" :description="$taxonomy->description" />
        @endif

        <x-backend.card>
            <x-slot name="header">
                Taxonomy Terms: {{ $taxonomy->name }}
            </x-slot>

            <x-slot name="headerActions">
                @if ($logged_in_user->hasPermissionTo('user.access.taxonomy.data.editor'))
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.taxonomy.terms.create', $taxonomy)" :text="__('Create Term')">
                    </x-utils.link>

                    <x-utils.link icon="c-icon cil-pencil" class="card-header-action" :href="route('dashboard.taxonomy.edit', $taxonomy)" :text="__('Edit Taxonomy')">
                    </x-utils.link>
                @endif
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissable="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.taxonomy-term-table :taxonomy="$taxonomy" />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
