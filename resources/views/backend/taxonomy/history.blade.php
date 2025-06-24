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
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Changes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>{{ $activity['created_at'] }}</td>
                                    <td>{{ $activity['causer']['name'] ?? 'System' }}</td>
                                    <td>
                                        @php
                                            $subject = $activity['subject'];
                                            $subjectName =
                                                $subject['name'] ??
                                                ($subject['file_name'] ?? '#' . $activity['subject_id']);
                                        @endphp
                                        {{ class_basename($activity['subject_type']) }}: {{ $subjectName }}
                                    </td>
                                    <td>{{ $activity['description'] }}</td>
                                    <td>
                                        @if ($activity['properties']['attributes'] ?? false)
                                            <ul class="mb-0 list-unstyled">
                                                @foreach ($activity['properties']['attributes'] as $field => $new)
                                                    @php $old = $activity['properties']['old'][$field] ?? null; @endphp
                                                    <li>
                                                        <strong>{{ $field }}</strong>:
                                                        @if (is_bool($old) && is_bool($new))
                                                            {{-- If boolean values  --}}
                                                            {{ $old ? 1 : 0 }} &rarr; {{ $new ? 1 : 0 }}
                                                        @elseif (is_array($old) || is_array($new))
                                                            {{-- if arrays  --}}
                                                            <div class="container">
                                                                <div>
                                                                    @if (is_array($old))
                                                                        <ul class="mb-0 list-unstyled">
                                                                            @foreach ($old as $key => $value)
                                                                                @if ($value !== '...')
                                                                                    <li>
                                                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                                                    </li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        {{ $old }}
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    &rarr;
                                                                </div>
                                                                <div>
                                                                    @if (is_array($new))
                                                                        <ul class="mb-0 list-unstyled">
                                                                            @foreach ($new as $key => $value)
                                                                                @if ($value !== '...')
                                                                                    <li>
                                                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                                                    </li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        {{ $new }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @else
                                                            {{-- otherwise  --}}
                                                            {{ $old }} &rarr; {{ $new }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('No activity recorded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
