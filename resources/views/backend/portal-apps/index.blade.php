@extends('backend.layouts.app')

@section('title', __('Portal Apps'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            @lang('Portal Apps & API Keys')
        </x-slot>

        <x-slot name="body">
            <livewire:backend.portal-app-table />
        </x-slot>
    </x-backend.card>

    <form method="POST" action="{{ route('dashboard.services.apps.store') }}">
        <x-backend.card>
            <x-slot name="header">
                Create New App
            </x-slot>

            <x-slot name="body">
                @csrf

                <div class="row g-3 align-items-center mb-2">
                    <div class="col-2">
                        <label for="name" class="col-form-label">@lang('Name')*</label>
                    </div>
                    <div class="col-3">
                        <input type="text" name="name" class="form-control" required />
                    </div>
                    <div class="col-auto">
                        <span id="passwordHelpInline" class="form-text">
                            Portal Integration App name
                        </span>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <button class="btn btn-primary btn-w-150 float-end ms-2" type="submit">
                    @lang('Create')
                </button>
            </x-slot>
        </x-backend.card>
    </form>
@endsection
