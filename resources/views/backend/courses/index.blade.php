@extends('backend.layouts.app')

@section('title', __('Manage'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Courses
            </x-slot>

            @if (1)
                <x-slot name="headerActions">
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action"  :text="__('Create Course')">
                    </x-utils.link>
                </x-slot>
            @endif
        </x-backend.card>
    </div>
@endsection
