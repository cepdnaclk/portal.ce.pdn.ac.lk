@extends('backend.layouts.app')

@section('title', $title)

@section('content')
    <x-forms.post :action="$submitRoute" enctype="multipart/form-data">
        <x-backend.card>
            <x-slot name="header">{{ $title }}</x-slot>
            <x-slot name="headerActions">
                <x-utils.link class="card-header-action" :href="$cancelRoute" :text="__('Cancel')" />
            </x-slot>
            <x-slot name="body">
                @include('profile.includes.form')
            </x-slot>
            <x-slot name="footer">
                <button class="btn btn-sm btn-primary float-end" type="submit">@lang('Save Profile')</button>
            </x-slot>
        </x-backend.card>
    </x-forms.post>
@endsection
