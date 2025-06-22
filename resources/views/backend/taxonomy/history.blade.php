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
                                    <td>{{ $activity->created_at }}</td>
                                    <td>{{ $activity->causer->name ?? 'System' }}</td>
                                    <td>
                                        @php
                                            $subject = $activity->subject;
                                            $subjectName =
                                                $subject?->name ??
                                                ($subject?->file_name ?? '#' . $activity->subject_id);
                                        @endphp
                                        {{ class_basename($activity->subject_type) }}: {{ $subjectName }}
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td>
                                        @if ($activity->properties['attributes'] ?? false)
                                            <ul class="mb-0 list-unstyled">
                                                @foreach ($activity->properties['attributes'] as $field => $new)
                                                    @php $old = $activity->properties['old'][$field] ?? null; @endphp
                                                    <li>
                                                        <strong>{{ $field }}</strong>:
                                                        @if (is_bool($old))
                                                            {{ $old ? 1 : 0 }}
                                                        @elseif (is_array($old))
                                                            {{ json_encode($old) }}
                                                        @else
                                                            {{ $old }}
                                                        @endif
                                                        &rarr;
                                                        @if (is_bool($new))
                                                            {{ $new ? 1 : 0 }}
                                                        @elseif (is_array($new))
                                                            {{ json_encode($new) }}
                                                        @else
                                                            {{ $new }}
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
