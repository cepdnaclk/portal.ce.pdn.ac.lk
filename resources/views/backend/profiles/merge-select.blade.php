@extends('backend.layouts.app')

@section('title', __('Merge Profiles'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            {{ __('Merge Profiles') }}
        </x-slot>

        <x-slot name="body">
            <x-utils.alert type="info" :dismissable="false">
                {{ __('Select two profiles to compare. You will choose which profile to keep before the merge is applied.') }}
            </x-utils.alert>

            @if ($errors->any())
                <x-utils.alert type="danger" :dismissable="false">
                    {{ $errors->first() }}
                </x-utils.alert>
            @endif

            {!! Form::open(['url' => route('dashboard.profiles.merge.review'), 'method' => 'post']) !!}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('First Profile') }}</label>
                    <livewire:backend.searchable-dropdown name="profile_ids[0]" :options="$profileOptions" :selected="old('profile_ids.0')"
                        :placeholder="__('Search by email or name')" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Second Profile') }}</label>
                    <livewire:backend.searchable-dropdown name="profile_ids[1]" :options="$profileOptions" :selected="old('profile_ids.1')"
                        :placeholder="__('Search by email or name')" />
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('dashboard.profiles.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Compare Profiles') }}</button>
            </div>
            {!! Form::close() !!}
        </x-slot>
    </x-backend.card>
@endsection
