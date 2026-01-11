@extends('backend.layouts.app')

@section('title', __('Create Tenant'))

@section('content')
    <x-forms.post :action="route('dashboard.tenants.store')">
        <x-backend.card>
            <x-slot name="header">
                @lang('Create Tenant')
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link class="card-header-action" :href="route('dashboard.tenants.index')" :text="__('Cancel')" />
            </x-slot>

            <x-slot name="body">
                @include('backend.tenant.includes.form')
            </x-slot>

            <x-slot name="footer">
                <button class="btn btn-sm btn-primary float-end" type="submit">@lang('Create Tenant')</button>
            </x-slot>
        </x-backend.card>
    </x-forms.post>
@endsection
