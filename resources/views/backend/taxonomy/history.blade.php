@extends('backend.layouts.app')

@section('title', __('Taxonomy History'))

@section('content')
    <div class="container mt-4">
        <h3>{{ __('Change History for') }}: {{ $taxonomy->name }}</h3>

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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at }}</td>
                                    <td>{{ $activity->causer->name ?? 'System' }}</td>
                                    <td>{{ $activity->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('No activity recorded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
