@extends('backend.layouts.app')

@section('title', __('Email Service'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            @lang('Portal Apps & API Keys')
        </x-slot>

        <x-slot name="headerActions">
            <x-utils.link class="card-header-action" :href="route('dashboard.email-service.history')" :text="__('Email History')" />
        </x-slot>

        <x-slot name="body">
            @if (session('new_api_key'))
                <div class="alert alert-warning">
                    <strong>@lang('New API Key')</strong>
                    <div class="text-monospace mt-2">{{ session('new_api_key.key') }}</div>
                    <div class="text-muted mt-1">@lang('Copy this key now. It will not be shown again.')
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.email-service.senders.store') }}" class="row g-3 mb-4">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">@lang('Name')</label>
                    <input type="text" name="name" class="form-control" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label">@lang('Status')</label>
                    <select name="status" class="form-select">
                        <option value="active">@lang('Active')</option>
                        <option value="revoked">@lang('Revoked')</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary" type="submit">@lang('Create')</button>
                </div>
            </form>

            @foreach ($portalApps as $portalApp)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $portalApp->name }}</strong>
                            <span class="badge bg-{{ $portalApp->status === 'active' ? 'success' : 'secondary' }} ms-2">
                                {{ ucfirst($portalApp->status) }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('dashboard.email-service.keys.generate', $portalApp) }}" class="d-flex">
                            @csrf
                            <input type="date" name="expires_at" class="form-control form-control-sm me-2" placeholder="@lang('Expires')" />
                            <button class="btn btn-sm btn-outline-primary" type="submit">@lang('Generate Key')</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>@lang('Key Prefix')</th>
                                        <th>@lang('Created')</th>
                                        <th>@lang('Last Used')</th>
                                        <th>@lang('Expires')</th>
                                        <th>@lang('Revoked')</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($portalApp->apiKeys as $apiKey)
                                        <tr>
                                            <td class="text-monospace">{{ $apiKey->key_prefix ?? '-' }}</td>
                                            <td>{{ $apiKey->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                            <td>{{ $apiKey->last_used_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                            <td>{{ $apiKey->expires_at?->format('Y-m-d') ?? '-' }}</td>
                                            <td>{{ $apiKey->revoked_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                            <td class="text-end">
                                                @if (!$apiKey->revoked_at)
                                                    <form method="POST" action="{{ route('dashboard.email-service.keys.revoke', $apiKey) }}">
                                                        @csrf
                                                        <button class="btn btn-sm btn-outline-danger" type="submit">@lang('Revoke')</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">@lang('Revoked')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">@lang('No API keys yet.')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </x-slot>
    </x-backend.card>
@endsection
