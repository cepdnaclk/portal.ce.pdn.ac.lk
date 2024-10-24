@extends('backend.layouts.app')

@section('title', __('Course'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Courses : Delete | {{ $course->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $course->name }}"</i></strong> ?
                </p>

                @if ($course->prerequisiteFor()->exists())
                    <div class="alert alert-warning">
                        <strong>Warning:</strong>
                        This course is a prerequisite for the following
                        course{{ $course->prerequisiteFor->count() == 1 ? '' : 's' }}:
                        <ul>
                            @foreach ($course->prerequisiteFor as $dependentCourse)
                                <li>{{ $dependentCourse->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.courses.destroy', compact('course')),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('dashboard.courses.index') }}" class="btn btn-light mr-2">Back</a>
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
