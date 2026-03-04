@extends('backend.layouts.app')

@section('title', __('Tenant Users'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            {{ __('Users Assigned to :tenant', ['tenant' => $tenant->name]) }}
        </x-slot>

        <x-slot name="headerActions">
            <x-utils.link icon="c-icon cil-arrow-left" class="card-header-action" :href="route('dashboard.tenants.index')" :text="__('Back to Tenants')" />
        </x-slot>

        <x-slot name="body">
            <livewire:backend.tenant-assigned-users-table :tenant="$tenant" />
        </x-slot>
    </x-backend.card>
@endsection
