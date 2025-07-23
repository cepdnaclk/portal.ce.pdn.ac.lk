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
                    <x-utils.alert type="success" dismissible="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.news-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
