@extends('backend.layouts.app')

@section('title', __('Taxonomy Terms'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy Terms
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('taxonomy-terms.create')" :text="__('Create Term')">
                </x-utils.link>
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

                <livewire:backend.taxonomy-term-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
