@extends('backend.layouts.app')

@section('title', __('Semesters'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Semesters
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.semesters.create')" :text="__('Create Semester')">
                </x-utils.link>
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <x-utils.alert type="success" dismissible="true">{{ session('Success') }}</x-utils.alert>
                @endif

                @livewire('backend.semester-table')
            </x-slot>
        </x-backend.card>
    </div>
@endsection
