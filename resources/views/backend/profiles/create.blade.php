@extends('backend.layouts.app')

@section('title', __('Create Profile'))

@section('content')
    {!! Form::open([
        'url' => route('dashboard.profiles.store'),
        'method' => 'post',
        'class' => 'container',
    ]) !!}

    <x-backend.card>
        <x-slot name="header">
            {{ __('Profile : Create') }}
        </x-slot>

        <x-slot name="headerActions">
            <x-utils.link class="card-header-action" :href="route('dashboard.profiles.index')" :text="__('Cancel')" />
        </x-slot>

        <x-slot name="body">
            @include('backend.profiles._form')
        </x-slot>

        <x-slot name="footer">
            {!! Form::submit(__('Create'), ['class' => 'btn btn-primary btn-w-150 float-end', 'id' => 'submit-button']) !!}
        </x-slot>
    </x-backend.card>

    {!! Form::close() !!}
@endsection
