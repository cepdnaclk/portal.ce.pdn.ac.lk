@extends('backend.layouts.app')

@section('title', __('Delete Semester'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Semester : Delete | {{ $semester->title }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $semester->title }}"</i></strong> ?
                </p>

                @if ($courses->count() > 0)
                    <p>The following courses are linked to this semester. Deletion is not permitted until these courses are
                        reassigned or deleted.</p>
                    <ul>
                        @foreach ($courses as $course)
                            <li>
                                <a href="{{ route('dashboard.courses.edit', $course->id) }}">{{ $course->code }} -
                                    {{ $course->name }} </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('dashboard.semesters.index') }}" class="btn btn-light mr-2">Back</a>
                @else
                    <div class="d-flex">
                        {!! Form::open([
                            'url' => route('dashboard.semesters.destroy', $semester),
                            'method' => 'delete',
                            'class' => 'container',
                        ]) !!}
                        <a href="{{ route('dashboard.semesters.index') }}" class="btn btn-light mr-2">Back</a>
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    </div>
                @endif
            </x-slot>
        </x-backend.card>
    </div>
@endsection
