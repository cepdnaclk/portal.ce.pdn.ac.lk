@extends('frontend.layouts.app')

@section('title', __('Intranet'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <x-frontend.card>
                    <x-slot name="header">
                        @lang('Intranet')
                    </x-slot>

                    <x-slot name="body">
                        <p>@lang('You are logged in!')</p>

                        @if ($profile && $profile->completeness < 100)
                            <div class="progress mb-2" style="max-width: 400px;"
                                aria-label="{{ __('Profile completeness') }}">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $profile->completeness }}%"
                                    aria-valuenow="{{ $profile->completeness }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $profile->completeness }}%
                                </div>
                            </div>
                            <p class="text-muted">
                                {{ __('Your profile is :percent% complete.', ['percent' => $profile->completeness]) }}
                            </p>
                        @endif

                        <x-utils.link :text="__('Manage My Profile')" class="btn btn-primary"
                            :href="route('intranet.user.profile.manage')" />
                    </x-slot>
                </x-frontend.card>
            </div><!--col-md-10-->
        </div><!--row-->
    </div><!--container-->
@endsection
