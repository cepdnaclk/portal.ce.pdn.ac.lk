@extends('backend.layouts.app')

@section('title', __('Update Tenant'))

@section('content')
    <x-forms.patch :action="route('dashboard.tenants.update', $tenant)">
        <x-backend.card>
            <x-slot name="header">
                @lang('Update Tenant')
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link class="card-header-action" :href="route('dashboard.tenants.index')" :text="__('Cancel')" />
            </x-slot>

            <x-slot name="body">
                @include('backend.tenant.includes.form', ['tenant' => $tenant])
            </x-slot>

            <x-slot name="footer">
                <button class="btn btn-sm btn-primary float-end" type="submit">@lang('Update Tenant')</button>
            </x-slot>
        </x-backend.card>
    </x-forms.patch>
@endsection
