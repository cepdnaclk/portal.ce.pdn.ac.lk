@extends('backend.layouts.app')

@section('title', __('Delete Profile'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                {{ __('Profile') }} : {{ __('Delete') }} | {{ $profile->preferred_long_name ?: $profile->email }}
            </x-slot>

            <x-slot name="body">
                <p>
                    @lang('Are you sure you want to delete')

                    <strong><i>
                            @if ($profile->preferred_long_name)
                                {{ $profile->preferred_long_name }} ({{ $profile->email }})
                            @else
                                {{ $profile->email }}
                            @endif
                        </i>
                    </strong>?
                </p>

                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.profiles.destroy', $profile),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('dashboard.profiles.index') }}" class="btn btn-light mr-2">@lang('Back')</a>
                    {!! Form::submit(__('Delete'), ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
