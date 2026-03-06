@extends('backend.layouts.app')

@section('title', __('Role Users'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            {{ __('Users Assigned to :role', ['role' => $role->name]) }}
        </x-slot>

        <x-slot name="headerActions">
            <x-utils.link icon="c-icon cil-arrow-left" class="card-header-action" :href="route('dashboard.auth.role.index')" :text="__('Back to Roles')" />
        </x-slot>

        <x-slot name="body">
            <livewire:backend.role-assigned-users-table :role="$role" />
        </x-slot>
    </x-backend.card>
@endsection
