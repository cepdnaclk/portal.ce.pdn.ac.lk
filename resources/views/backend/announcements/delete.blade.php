@extends('backend.layouts.app')

@section('title', __('Announcement'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Announcements : Delete | {{ $announcement->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $announcement->message }}"</i></strong> ?
                </p>
                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.announcements.destroy', compact('announcement')),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-light mr-2">Back</a>
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
