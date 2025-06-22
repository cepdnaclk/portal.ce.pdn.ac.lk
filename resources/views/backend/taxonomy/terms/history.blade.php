@extends('backend.layouts.app')

@section('title', __('Taxonomy Term History'))

@section('content')
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
                                    <td>{{ $activity->created_at }}</td>
                                    <td>{{ $activity->causer->name ?? 'System' }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>
                                        @if ($activity->properties['attributes'] ?? false)
                                            <ul class="mb-0 list-unstyled">
                                                @foreach($activity->properties['attributes'] as $field => $new)
                                                    @php $old = $activity->properties['old'][$field] ?? null; @endphp
                                                    <li><strong>{{ $field }}</strong>: {{ $old }} &rarr; {{ $new }}</li>
                                                @endforeach
                                            </ul>
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
