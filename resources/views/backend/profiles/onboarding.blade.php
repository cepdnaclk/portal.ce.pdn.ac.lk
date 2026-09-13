@extends('backend.layouts.app')

@section('title', __('Complete Your Profile'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            {{ __('Complete Your Profile') }}
        </x-slot>

        <x-slot name="body">
            <livewire:backend.profile-wizard />
        </x-slot>
    </x-backend.card>
@endsection
