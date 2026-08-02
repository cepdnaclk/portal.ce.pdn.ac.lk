@php
    $bannerProfile = $logged_in_user?->loadMissing('profile')->profile;
@endphp

@if (
    $logged_in_user &&
        (!$bannerProfile || !$bannerProfile->isComplete()) &&
        !request()->routeIs('dashboard.profiles.onboarding'))
    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3" role="alert">
        <span>
            <i class="fa fa-exclamation-triangle me-1"></i>
            {{ __('Your profile is :percent% complete — please complete your profile.', ['percent' => $bannerProfile?->completeness ?? 0]) }}
        </span>
        <a href="{{ route('dashboard.profiles.onboarding') }}" class="btn btn-sm btn-primary ms-3">
            {{ __('Complete Profile') }}
        </a>
    </div>
@endif
