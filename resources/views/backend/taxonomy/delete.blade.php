@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy : Delete | {{ $taxonomy->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $taxonomy->name }}"</i></strong> ?
                </p>

                @if ($terms->count() > 0)
                    <p>The following terms are linked to this Taxonomy. Deletion is not permitted until these terms are
                        reassigned or deleted.</p>
                    <ul>
                        @foreach ($terms as $term)
                            <li>
                                <a href="{{ route('dashboard.taxonomy.terms.edit', compact('taxonomy', 'term')) }}">
                                    {{ $term->name }} ({{ $term->code }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('dashboard.semesters.index') }}" class="btn btn-light mr-2">Back</a>
                @else
                    <div class="d-flex">
                        {!! Form::open([
                            'url' => route('dashboard.taxonomy.destroy', compact('taxonomy')),
                            'method' => 'delete',
                            'class' => 'container',
                        ]) !!}

                        <a href="{{ route('dashboard.taxonomy.index') }}" class="btn btn-light mr-2">Back</a>
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    </div>
                @endif

            </x-slot>
        </x-backend.card>
    </div>
@endsection
