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
                @endif


                <x-slot name="footer">
                    @if ($terms->count() == 0)
                        {!! Form::open([
                            'url' => route('dashboard.taxonomy.destroy', compact('taxonomy')),
                            'method' => 'delete',
                        ]) !!}

                        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-w-150 float-end']) !!}
                        {!! Form::close() !!}
                    @endif
                    <a href="{{ route('dashboard.taxonomy.index') }}"
                        class="btn btn-light btn-outline-secondary btn-w-150 float-end mr-2">Back</a>
                </x-slot>

            </x-slot>
        </x-backend.card>
    </div>
@endsection
