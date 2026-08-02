<div>
    @if ($step < 4)
        <div class="row align-items-center mb-4">
            <div class="col-md-2">
                <strong>{{ __('Progress') }}</strong>
            </div>
            <div class="col-md-10">
                <div class="progress" aria-label="{{ __('Profile completeness') }}">
                    <div class="progress-bar {{ $before >= 50 ? 'bg-success' : 'bg-warning' }}" role="progressbar"
                        style="width: {{ $before }}%" aria-valuenow="{{ $before }}" aria-valuemin="0"
                        aria-valuemax="100">
                        {{ $before }}%
                    </div>
                </div>
                <small class="form-text text-muted">{{ __('Current profile completeness') }}</small>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" aria-label="{{ __('Profile setup steps') }}">
            @foreach ([1 => __('Names'), 2 => __('Location & Affiliation'), 3 => __('Links')] as $number => $label)
                <li class="nav-item">
                    <span
                        class="nav-link {{ $step === $number ? 'active' : '' }} {{ $step < $number ? 'disabled' : '' }}">
                        {{ $number }}. {{ $label }}
                    </span>
                </li>
            @endforeach
        </ul>

        @if ($profileTypes->isNotEmpty())
            <div class="form-group row">
                <div class="col-md-2 col-form-label">{{ __('Profile Types') }}</div>
                <div class="col-md-10">
                    @foreach ($profileTypes as $profileType)
                        <span class="badge bg-info">{{ str_replace('_', ' ', $profileType->type) }}</span>
                    @endforeach
                    <small class="form-text text-muted d-block">
                        {{ __('Profile types are assigned by administrators or data sync.') }}
                    </small>
                </div>
            </div>
        @endif
    @endif

    @if ($step === 1)
        <h5 class="border-bottom pb-2 mb-3">{{ __('Names') }}</h5>

        <div class="form-group row">
            <label class="col-md-2 col-form-label" for="wizard_honorific">@lang('Honorific')</label>
            <div class="col-md-3">
                <select id="wizard_honorific" class="form-select" wire:model.defer="fields.honorific">
                    <option value="">{{ __('Select an honorific') }}</option>
                    @foreach (\App\Domains\Profile\Models\UserProfile::HONORIFIC_OPTIONS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('fields.honorific')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>

        @foreach ([
        'full_name' => __('Full Name'),
        'name_with_initials' => __('Name with Initials'),
        'preferred_short_name' => __('Preferred Short Name'),
        'preferred_long_name' => __('Preferred Long Name'),
    ] as $field => $label)
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="wizard_{{ $field }}">{{ $label }}</label>
                <div class="col-md-10">
                    <input type="text" id="wizard_{{ $field }}" class="form-control"
                        wire:model.defer="fields.{{ $field }}" />
                    @error("fields.$field")
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            </div>
        @endforeach
    @elseif ($step === 2)
        <h5 class="border-bottom pb-2 mb-3">{{ __('Location & Affiliation') }}</h5>

        @foreach ([
        'location' => __('Location'),
        'current_affiliation' => __('Current Affiliation'),
        'current_position' => __('Current Position'),
    ] as $field => $label)
            <div class="form-group row">
                <label class="col-md-2 col-form-label" for="wizard_{{ $field }}">{{ $label }}</label>
                <div class="col-md-10">
                    <input type="text" id="wizard_{{ $field }}" class="form-control"
                        wire:model.defer="fields.{{ $field }}" />
                    @error("fields.$field")
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            </div>
        @endforeach

        <div class="form-group row">
            <label class="col-md-2 col-form-label" for="wizard_profile_image">@lang('Profile Picture')</label>
            <div class="col-md-10">
                @if ($profileImage)
                    <div class="mb-2">
                        <img src="{{ $profileImage->temporaryUrl() }}" class="img-thumbnail" style="max-height: 150px;"
                            alt="{{ __('Selected profile picture') }}" />
                    </div>
                @elseif ($profile->profileImageUrl())
                    <div class="mb-2">
                        <img src="{{ $profile->profileImageUrl() }}" class="img-thumbnail" style="max-height: 150px;"
                            alt="{{ __('Current profile picture') }}" />
                    </div>
                @endif
                <input type="file" id="wizard_profile_image" class="form-control" accept="image/jpeg"
                    wire:model="profileImage" />
                <small class="form-text text-muted">
                    {{ __('JPEG only. Maximum size: :size MB. Minimum dimensions: :width×:height pixels.', [
                        'size' => config('profile.image.max_file_size') / 1024,
                        'width' => config('profile.image.min_width'),
                        'height' => config('profile.image.min_height'),
                    ]) }}
                </small>
                <div class="text-muted" wire:loading wire:target="profileImage">{{ __('Uploading…') }}</div>
                @error('profileImage')
                    <strong class="text-danger d-block">{{ $message }}</strong>
                @enderror
            </div>
        </div>
    @elseif ($step === 3)
        <h5 class="border-bottom pb-2 mb-3">{{ __('Links') }}</h5>

        @foreach (\App\Domains\Profile\Models\UserProfileLink::LINK_TYPE_LABELS as $linkType => $linkLabel)
            <div class="form-group row">
                <label class="col-md-2 col-form-label"
                    for="wizard_link_{{ $linkType }}">{{ $linkLabel }}</label>
                <div class="col-md-10">
                    <input type="url" id="wizard_link_{{ $linkType }}" class="form-control"
                        placeholder="https://example.com" wire:model.defer="links.{{ $linkType }}" />
                    @error("links.$linkType")
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-4">
            <i class="fa fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
            <h4>{{ __('Profile updated') }}</h4>
            <p class="text-muted">
                {{ __('Completeness went from :before% to :after%.', ['before' => $before, 'after' => $after]) }}
            </p>
            <div class="progress mx-auto" style="max-width: 400px;" aria-label="{{ __('Profile completeness') }}">
                <div class="progress-bar {{ $after >= 50 ? 'bg-success' : 'bg-warning' }}" role="progressbar"
                    style="width: {{ $after }}%" aria-valuenow="{{ $after }}" aria-valuemin="0"
                    aria-valuemax="100">
                    {{ $after }}%
                </div>
            </div>
            <a href="{{ route('dashboard.home') }}" class="btn btn-primary mt-4">{{ __('Back to Dashboard') }}</a>
        </div>
    @endif

    @if ($step < 4)
        <div class="border-top pt-3 mt-4 d-flex justify-content-between">
            <button type="button" class="btn btn-sm btn-light btn-w-150" wire:click="back"
                wire:loading.attr="disabled" @if ($step === 1) disabled @endif>
                {{ __('Back') }}
            </button>

            @if ($step < 3)
                <button type="button" class="btn btn-sm btn-primary btn-w-150" wire:click="next"
                    wire:loading.attr="disabled">
                    {{ __('Next') }}
                </button>
            @else
                <button type="button" class="btn btn-sm btn-success btn-w-150" wire:click="finish"
                    wire:loading.attr="disabled">
                    {{ __('Finish') }}
                </button>
            @endif
        </div>
    @endif
</div>
