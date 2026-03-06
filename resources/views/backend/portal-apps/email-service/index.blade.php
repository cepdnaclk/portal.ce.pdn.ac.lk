@extends('backend.layouts.app')

@section('title', __('Email History'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            @lang('Email History')

        </x-slot>

        <x-slot name="headerActions">
            <button type="button" class="btn btn-link text-muted text-decoration-none btn-sm rounded-pill"
                data-bs-toggle="popover" data-bs-html="true" data-bs-placement="left" data-bs-trigger="click"
                data-bs-content="Email sending can be done using the Email API. Please refer to <a href='https://cepdnaclk.github.io/portal.ce.pdn.ac.lk/features/email-api.html'>documentation</a> for details.">
                <i class='fa fa-lg fa-info-circle mx-1'></i>
            </button>
        </x-slot>

        <x-slot name="body">
            <livewire:backend.email-delivery-logs-table />
        </x-slot>
    </x-backend.card>
@endsection
