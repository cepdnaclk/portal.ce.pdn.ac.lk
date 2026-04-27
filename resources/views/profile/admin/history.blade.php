@extends('backend.layouts.app')

@section('title', __('Profile History'))

@section('content')
    @push('after-styles')
        <style>
            {!! $diffCss !!}
        </style>
    @endpush

    <x-backend.card>
        <x-slot name="header">
            {{ __('Profile History') }}: {{ $profile->preferred_long_name ?: $profile->email }}
        </x-slot>
        <x-slot name="body">
            @include('profile.includes.history-table')
        </x-slot>
    </x-backend.card>
@endsection
