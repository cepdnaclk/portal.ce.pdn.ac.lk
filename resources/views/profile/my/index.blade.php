@extends('backend.layouts.app')

@section('title', __('My Profiles'))

@section('content')
    @if (empty($existingTypes))
        <div class="alert alert-warning">
            @lang('No profiles are linked to your account yet.')
        </div>
    @endif

    @if (session('profiles.has_incomplete'))
        <div class="alert alert-info">
            @lang('Some profiles are incomplete. Complete the required fields to improve visibility and consistency.')
        </div>
    @endif

    <x-backend.card>
        <x-slot name="header">@lang('My Profiles')</x-slot>
        <x-slot name="headerActions">
            @php
                $missingTypes = array_values(array_diff($availableTypes, $existingTypes));
            @endphp
            @foreach ($missingTypes as $type)
                <x-utils.link class="card-header-action" :href="route('dashboard.my-profiles.create', ['type' => $type])" :text="__('Create :type', [
                    'type' => \App\Domains\Profiles\Models\Profile::TYPE_LABELS[$type] ?? $type,
                ])" />
            @endforeach
        </x-slot>
        <x-slot name="body">
            @if (empty($availableTypes))
                <div class="alert alert-warning mb-3">
                    @lang('No profile types are available for you, based on currently assigned roles.')
                </div>
            @endif

            <livewire:backend.my-profiles-table />
        </x-slot>
    </x-backend.card>
@endsection
