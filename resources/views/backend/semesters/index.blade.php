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
                    <div class="alert alert-success">
                        {{ session('Success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @livewire('backend.semester-table')
            </x-slot>
        </x-backend.card>
    </div>
@endsection
