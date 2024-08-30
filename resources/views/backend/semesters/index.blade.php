@extends('backend.layouts.app')

@section('title', __('Manage'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Semesters
            </x-slot>

            @if (1)
                <x-slot name="headerActions">
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.semesters.create')" :text="__('Create Semester')">
                    </x-utils.link>
                </x-slot>
            @endif
        </x-backend.card>
    </div>
@endsection
