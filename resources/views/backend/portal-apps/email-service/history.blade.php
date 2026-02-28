@extends('backend.layouts.app')

@section('title', __('Portal Apps - Email Service'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            @lang('Email History')
        </x-slot>

        <x-slot name="body">
            <form method="GET" action="{{ route('dashboard.services.email.history') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">@lang('Portal App')</label>
                    <select name="portal_app" class="form-select">
                        <option value="">@lang('All')</option>
                        @foreach ($portalApps as $portalApp)
                            <option value="{{ $portalApp->id }}" @if (request('portal_app') === $portalApp->id) selected @endif>
                                {{ $portalApp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">@lang('Status')</label>
                    <select name="status" class="form-select">
                        <option value="">@lang('All')</option>
                        <option value="queued" @if (request('status') === 'queued') selected @endif>@lang('Queued')</option>
                        <option value="sent" @if (request('status') === 'sent') selected @endif>@lang('Sent')</option>
                        <option value="failed" @if (request('status') === 'failed') selected @endif>@lang('Failed')</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">@lang('From Date')</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" />
                </div>

                <div class="col-md-2">
                    <label class="form-label">@lang('To Date')</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" />
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary me-2" type="submit">@lang('Filter')</button>
                    <a class="btn btn-outline-secondary"
                        href="{{ route('dashboard.services.email.history') }}">@lang('Reset')</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('ID')</th>
                            <th>@lang('Portal App')</th>
                            <th>@lang('Subject')</th>
                            <th>@lang('Recipients')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Sent At')</th>
                            <th>@lang('Created')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="text-monospace">{{ $log->id }}</td>
                                <td>{{ $log->portalApp?->name ?? '-' }}</td>
                                <td>{{ $log->subject }}</td>
                                <td>
                                    <div>{{ implode(', ', $log->to ?? []) }}</div>
                                    @if (!empty($log->cc))
                                        <div class="text-muted">@lang('CC'): {{ implode(', ', $log->cc) }}</div>
                                    @endif
                                    @if (!empty($log->bcc))
                                        <div class="text-muted">@lang('BCC'): {{ implode(', ', $log->bcc) }}</div>
                                    @endif
                                </td>
                                <td>{{ ucfirst($log->status) }}</td>
                                <td>{{ $log->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $log->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">@lang('No email deliveries found.')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </x-slot>
    </x-backend.card>
@endsection
