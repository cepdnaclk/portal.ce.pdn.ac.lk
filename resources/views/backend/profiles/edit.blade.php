@extends('backend.layouts.app')

@section('title', __('Edit Profile'))

@section('content')
    {!! Form::open([
        'url' => route('dashboard.profiles.update', $profile),
        'method' => 'PUT',
        'class' => 'container',
    ]) !!}

    @csrf

    <x-backend.card>
        <x-slot name="header">
            Profile : Edit | {{ $profile->email }}
        </x-slot>

        <x-slot name="body">
            @include('backend.profiles._form', ['profile' => $profile])
        </x-slot>

        <x-slot name="footer">
            {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
        </x-slot>
    </x-backend.card>

    {!! Form::close() !!}
@endsection
