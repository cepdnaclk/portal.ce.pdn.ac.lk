@extends('backend.layouts.app')

@section('title', __('Create Profile'))

@section('content')
    {!! Form::open([
        'url' => route('dashboard.profiles.store'),
        'method' => 'POST',
        'class' => 'container',
    ]) !!}

    @csrf

    <x-backend.card>
        <x-slot name="header">
            Profile : Create
        </x-slot>

        <x-slot name="body">
            @include('backend.profiles._form')
        </x-slot>

        <x-slot name="footer">
            {!! Form::submit(__('Create'), ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
        </x-slot>
    </x-backend.card>

    {!! Form::close() !!}
@endsection
