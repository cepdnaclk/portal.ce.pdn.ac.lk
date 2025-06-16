@extends('backend.layouts.app')

@section('title', __('Taxonomy Pages'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy Pages') }}
            </x-slot>

            <x-slot name="body">
                @if (session('Success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('Success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <p>This page is under development</p>

            </x-slot>

        </x-backend.card>
    </div>
@endsection
