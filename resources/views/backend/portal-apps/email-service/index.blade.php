@extends('backend.layouts.app')

@section('title', __('Email History'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            @lang('Email History')
        </x-slot>

        <x-slot name="body">
            <livewire:backend.email-delivery-logs-table />
        </x-slot>
    </x-backend.card>
@endsection
