@extends('backend.layouts.app')

@section('title', __('Manage'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Courses
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link icon="c-icon cil-plus" class="card-header-action" :text="__('Create Course')">
                </x-utils.link>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
