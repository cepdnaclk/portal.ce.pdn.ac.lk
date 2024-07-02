@extends('backend.layouts.app')

@section('title', __('EventItem'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                News : Delete | {{ $eventItem->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $eventItem->title }}"</i></strong> ?
                </p>
                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.event.destroy', compact('eventItem')),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('dashboard.event.index') }}" class="btn btn-light mr-2">Back</a>
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
