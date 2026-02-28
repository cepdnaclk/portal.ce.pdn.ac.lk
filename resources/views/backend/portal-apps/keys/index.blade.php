@extends('backend.layouts.app')

@section('title', __('API Keys'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            API Keys for <b>@lang(':name', ['name' => $portalApp->name])</b>
        </x-slot>

        <x-slot name="body">
            @if (session('new_api_key'))
                <div class="alert alert-warning">
                    <strong>@lang('New API Key')</strong>
                    <div class="text-monospace mt-2">{{ session('new_api_key.key') }}</div>
                    <div class="text-muted mt-1">@lang('Copy this key now. It will not be shown again.')
                    </div>
                </div>
            @elseif (session('Success'))
                <div class="alert alert-success">
                    {{ session('Success') }}
                </div>
            @endif

            <livewire:backend.portal-app-api-keys-table :portalApp="$portalApp" />
        </x-slot>
    </x-backend.card>

    <form method="POST" action="{{ route('dashboard.services.apps.keys.generate', $portalApp) }}">
        <x-backend.card>
            <x-slot name="header">
                Generate New API Key
            </x-slot>

            <x-slot name="body">
                @csrf

                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="expires_at" class="col-form-label">@lang('Expires at')</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="expires_at" class="form-control" placeholder="@lang('Expires')" />
                    </div>
                    <div class="col-auto">
                        <span id="passwordHelpInline" class="form-text">
                            Optional
                        </span>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <button class="btn btn-primary btn-w-150 float-end ms-2" type="submit">
                    @lang('Generate')
                </button>
            </x-slot>
        </x-backend.card>
    </form>
@endsection
