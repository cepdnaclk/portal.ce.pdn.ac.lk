<div>
    @if ($step < 4)
        {{-- Step indicator --}}
        <div class="mb-4">
            <span class="badge {{ $step === 1 ? 'bg-primary' : 'bg-secondary' }}">1. {{ __('Names') }}</span>
            <span class="badge {{ $step === 2 ? 'bg-primary' : 'bg-secondary' }}">2.
                {{ __('Location & Affiliation') }}</span>
            <span class="badge {{ $step === 3 ? 'bg-primary' : 'bg-secondary' }}">3. {{ __('Links') }}</span>
        </div>

        <div class="mb-4">
            <div class="progress" style="max-width: 300px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $before }}%">
                    {{ $before }}%</div>
            </div>
            <small class="text-muted">{{ __('Current profile completeness') }}</small>
        </div>

        @if ($profileTypes->isNotEmpty())
            <div class="mb-4">
                <strong>{{ __('Profile Types') }}:</strong>
                @foreach ($profileTypes as $profileType)
                    <span class="badge bg-info">{{ $profileType->type }}</span>
                @endforeach
                <br>
                <small
                    class="text-muted">{{ __('Profile types are assigned by administrators or data sync.') }}</small>
            </div>
        @endif
    @endif

    @if ($step === 1)
        <div class="row">
            <div class="col-md-2 mb-3">
                <label class="form-label">@lang('Honorific')</label>
                <input type="text" class="form-control" maxlength="20" wire:model.defer="fields.honorific" />
                @error('fields.honorific')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">@lang('Full Name')</label>
                <input type="text" class="form-control" wire:model.defer="fields.full_name" />
                @error('fields.full_name')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">@lang('Name with Initials')</label>
                <input type="text" class="form-control" wire:model.defer="fields.name_with_initials" />
                @error('fields.name_with_initials')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">@lang('Preferred Short Name')</label>
                <input type="text" class="form-control" wire:model.defer="fields.preferred_short_name" />
                @error('fields.preferred_short_name')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">@lang('Preferred Long Name')</label>
                <input type="text" class="form-control" wire:model.defer="fields.preferred_long_name" />
                @error('fields.preferred_long_name')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
    @elseif ($step === 2)
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">@lang('Location')</label>
                <input type="text" class="form-control" wire:model.defer="fields.location" />
                @error('fields.location')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">@lang('Current Affiliation')</label>
                <input type="text" class="form-control" wire:model.defer="fields.current_affiliation" />
                @error('fields.current_affiliation')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">@lang('Current Position')</label>
                <input type="text" class="form-control" wire:model.defer="fields.current_position" />
                @error('fields.current_position')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">@lang('Profile Image URL')</label>
                <input type="text" class="form-control" wire:model.defer="fields.profile_image" />
                @error('fields.profile_image')
                    <strong class="text-danger">{{ $message }}</strong>
                @enderror
            </div>
        </div>
    @elseif ($step === 3)
        <div class="row">
            @foreach (\App\Domains\Profile\Models\UserProfileLink::LINK_TYPE_LABELS as $linkType => $linkLabel)
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ $linkLabel }}</label>
                    <input type="text" class="form-control" placeholder="https://…"
                        wire:model.defer="links.{{ $linkType }}" />
                    @error("links.$linkType")
                        <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            @endforeach
        </div>
    @else
        {{-- Done --}}
        <div class="text-center py-4">
            <h4 class="mb-3">{{ __('Profile updated!') }}</h4>
            <p>
                {{ __('Completeness went from :before% to :after%.', ['before' => $before, 'after' => $after]) }}
            </p>
            <div class="progress mx-auto" style="max-width: 300px;">
                <div class="progress-bar {{ $after >= 50 ? 'bg-success' : 'bg-warning' }}" role="progressbar"
                    style="width: {{ $after }}%">{{ $after }}%</div>
            </div>
            <a href="{{ route('dashboard.home') }}" class="btn btn-primary mt-4">{{ __('Back to Dashboard') }}</a>
        </div>
    @endif

    @if ($step < 4)
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-light" wire:click="back"
                @if ($step === 1) disabled @endif>
                {{ __('Back') }}
            </button>

            @if ($step < 3)
                <button type="button" class="btn btn-primary" wire:click="next">{{ __('Next') }}</button>
            @else
                <button type="button" class="btn btn-success" wire:click="finish">{{ __('Finish') }}</button>
            @endif
        </div>
    @endif
</div>
