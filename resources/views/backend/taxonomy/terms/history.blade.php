@extends('backend.layouts.app')

@section('title', __('Taxonomy Term History'))

@section('content')
    @push('after-styles')
        <style>
            {!! $diffCss !!}
        </style>
    @endpush
    <div class="container mt-4">
        <h3>{{ __('Change History for') }}: {{ $term->name }}</h3>

        <x-backend.card>
            <x-slot name="header">{{ __('History Log') }}</x-slot>
            <x-slot name="body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Changes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>{{ $activity['created_at'] }}</td>
                                    <td>{{ $activity['causer']['name'] ?? 'System' }}</td>
                                    <td>{{ $activity['description'] }}</td>
                                    <td>
                                        @if (!empty($activity['diffs']))
                                            @foreach ($activity['diffs'] as $field => $diff)
                                                <div class="mb-2">
                                                    <strong>{{ $field }}</strong>:
                                                    <div class="diff-wrapper">{!! $diff !!}</div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No activity recorded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
