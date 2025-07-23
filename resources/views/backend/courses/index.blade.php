@extends('backend.layouts.app')

@section('title', __('Courses'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Courses
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.courses.create')" :text="__('Create Course')">
                </x-utils.link>
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissible="true">{{ session('Success') }}</x-utils.alert>
                @endif

                @livewire('backend.course-table')
            </x-slot>
        </x-backend.card>
    </div>
@endsection
