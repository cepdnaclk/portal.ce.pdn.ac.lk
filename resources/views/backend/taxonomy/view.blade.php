@extends('backend.layouts.app')

@section('title', __('View Taxonomy'))

@section('content')

    <div class="container mt-4">

        <h3>Taxonomy: {{ $taxonomyData['name'] }} ({{ $taxonomyData['code'] }})</h3>

        <p>API Endpoint:
            <a target="_blank" href="{{ route('api.taxonomy.get', ['taxonomy_code' => $taxonomyData['code']]) }}">
                {{ route('api.taxonomy.get', ['taxonomy_code' => $taxonomyData['code']]) }}
            </a>
        </p>

        <h4>Terms: </h4>
        <pre class="p-3 border rounded">
{{ json_encode($taxonomyData['terms'], JSON_PRETTY_PRINT) }}
        </pre>
    </div>

@endsection
