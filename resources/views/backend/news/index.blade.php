@extends('backend.layouts.app')

@section('title', __('News'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                News
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.news.create')" :text="__('Create News')">
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

                <livewire:backend.news-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
