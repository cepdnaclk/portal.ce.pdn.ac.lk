@extends('backend.layouts.app')

@section('title', __('NewsItem'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                News : Delete | {{ $newsItem->id }}
            </x-slot>

            <x-slot name="body">
                <p>Are you sure you want to delete
                    <strong><i>"{{ $newsItem->title }}"</i></strong> ?
                </p>
                <div class="d-flex">
                    {!! Form::open([
                        'url' => route('dashboard.news.destroy', compact('newsItem')),
                        'method' => 'delete',
                        'class' => 'container',
                    ]) !!}

                    <a href="{{ route('dashboard.news.index') }}" class="btn btn-light mr-2">Back</a>
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}

                    {!! Form::close() !!}
                </div>
            </x-slot>
        </x-backend.card>
    </div>
@endsection
