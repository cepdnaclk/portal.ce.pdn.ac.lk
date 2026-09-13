@extends('backend.layouts.app')

@section('title', __('Delete Profile'))

@section('content')

    <x-backend.card>
        <x-slot name="header">
            Delete : Profile | {{ $profile->email }}
        </x-slot>

        <x-slot name="body">
            <p>Are you sure you want to delete the profile of
                <strong><i>"{{ $profile->full_name ?? $profile->email }}"</i></strong> ?
            </p>

            @if ($profile->user)
                <x-utils.alert type="warning">
                    {{ __('This profile is linked to the portal account ":name". The account itself will not be deleted.', ['name' => $profile->user->name]) }}
                </x-utils.alert>
            @endif
        </x-slot>

        <x-slot name="footer">
            {!! Form::open([
                'url' => route('dashboard.profiles.destroy', $profile),
                'method' => 'delete',
            ]) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger float-end btn-w-150']) !!}
            {!! Form::close() !!}
            <a href="{{ route('dashboard.profiles.index') }}" class="btn btn-light float-end btn-w-150 me-2">Back</a>
        </x-slot>
    </x-backend.card>
@endsection
