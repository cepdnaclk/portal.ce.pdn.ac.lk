@extends('backend.layouts.app')

@section('title', __('Delete Taxonomy Term'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Taxonomy Term: Delete | {{ $term->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $term->name }}"</i></strong> ?
                </p>

                @if ($term->children()->count() > 0)
                    <p>The following terms are linked to this Taxonomy Term, and will be deleted with this. </p>
                    <ul>
                        @foreach ($term->children()->get() as $childTerm)
                            <li>
                                <a href="{{ route('dashboard.taxonomy.terms.edit', ['taxonomy', 'childTerm']) }}">
                                    {{ $childTerm->name }} ({{ $childTerm->code }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <x-slot name="footer">
                    {!! Form::open([
                        'url' => route('dashboard.taxonomy.terms.destroy', ['taxonomy' => $taxonomy, 'term' => $term]),
                        'method' => 'delete',
                    ]) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-w-150 float-end']) !!}
                    {!! Form::close() !!}

                    <a href="{{ route('dashboard.taxonomy.terms.index', compact('taxonomy')) }}"
                        class="btn btn-light btn-outline-secondary btn-w-150 float-end mr-2">Back</a>
                </x-slot>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
