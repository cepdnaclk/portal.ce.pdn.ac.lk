@extends('backend.layouts.app')

@section('title', __('Articles'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Articles
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.article.create')" :text="__('Create Article')">
                </x-utils.link>
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissable="true">{{ session('Success') }}</x-utils.alert>
                @endif

                <livewire:backend.article-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
