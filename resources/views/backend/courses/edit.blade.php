@extends('backend.layouts.app')

@section('title', __('Courses'))

@section('content')
    <div>
        {{-- {!! Form::open([
            'url' => route('dashboard.courses.update', compact('course')),
            'method' => 'put',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!} --}}

        <x-backend.card>
            {{-- <x-slot name="header">
                Courses : Edit | {{ $course->id }}
            </x-slot> --}}

            <x-slot name="body">
                
            </x-slot>

            <x-slot name="footer">
                {{-- {!! Form::submit('Update', ['class' => 'btn btn-primary float-right']) !!} --}}
            </x-slot>

        </x-backend.card>
        {{-- {!! Form::close() !!} --}}
    </div>
@endsection
