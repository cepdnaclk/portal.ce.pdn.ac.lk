@extends('backend.layouts.app')

@section('title', __('Edit Profile'))

@section('content')
    {!! Form::open([
        'url' => route('dashboard.profiles.update', $profile),
        'method' => 'put',
        'class' => 'container',
        'files' => true,
        'enctype' => 'multipart/form-data',
    ]) !!}

    <x-backend.card>
        <x-slot name="header">
            {{ __('Profile : Edit') }} | {{ $profile->email }}
        </x-slot>

        <x-slot name="headerActions">
            <x-utils.link class="card-header-action" :href="route('dashboard.profiles.show', $profile)" :text="__('Cancel')" />
        </x-slot>

        <x-slot name="body">
            @include('backend.profiles._form', ['profile' => $profile])
        </x-slot>

        <x-slot name="footer">
            {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-w-150 float-end', 'id' => 'submit-button']) !!}
        </x-slot>
    </x-backend.card>

    {!! Form::close() !!}
@endsection
