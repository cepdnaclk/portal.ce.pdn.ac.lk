@extends('backend.layouts.app')

@section('title', __('Tenant Management'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            @lang('Tenant Management')
        </x-slot>

        <x-slot name="headerActions">
            <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.tenants.create')" :text="__('Create Tenant')" />
        </x-slot>

        <x-slot name="body">
            <livewire:backend.tenants-table />
        </x-slot>
    </x-backend.card>
@endsection
