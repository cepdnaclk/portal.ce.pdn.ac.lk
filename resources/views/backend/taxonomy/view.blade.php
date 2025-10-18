@extends('backend.layouts.app')

@section('title', __('View Taxonomy'))

@section('content')

    <div class="container mt-4">

        <x-backend.card>
            <x-slot name="header">
                Taxonomy: {{ $taxonomyData['name'] }} ({{ $taxonomyData['code'] }})
            </x-slot>

            <x-slot name="body">
                @if ($taxonomyData['visibility'])
                    <p>API Endpoint:
                        <a target="_blank" href="{{ route('api.taxonomy.get', ['taxonomy_code' => $taxonomyData['code']]) }}">
                            {{ route('api.taxonomy.get', ['taxonomy_code' => $taxonomyData['code']]) }}
                        </a>
                    </p>
                @endif

                <h4>Terms: </h4>
                <pre class="p-3 border rounded">
{{ json_encode($taxonomyData['terms'], JSON_PRETTY_PRINT) }}
        </pre>
            </x-slot>
        </x-backend.card>
    </div>

@endsection
