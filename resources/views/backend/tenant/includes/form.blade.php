@php
    $tenant = $tenant ?? null;
@endphp

<div class="form-group row">
    <label for="slug" class="col-md-2 col-form-label">@lang('Slug')</label>

    <div class="col-md-10">
        <input type="text" name="slug" class="form-control" placeholder="{{ __('Slug') }}"
            value="{{ old('slug', $tenant?->slug) }}" maxlength="255" required />
    </div>
</div>

<div class="form-group row">
    <label for="name" class="col-md-2 col-form-label">@lang('Name')</label>

    <div class="col-md-10">
        <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}"
            value="{{ old('name', $tenant?->name) }}" maxlength="255" required />
    </div>
</div>

<div class="form-group row">
    <label for="url" class="col-md-2 col-form-label">@lang('URL')</label>

    <div class="col-md-10">
        <input type="text" name="url" class="form-control" placeholder="{{ __('https://example.com') }}"
            value="{{ old('url', $tenant?->url) }}" maxlength="255" required />
    </div>
</div>

<div class="form-group row">
    <label for="description" class="col-md-2 col-form-label">@lang('Description')</label>

    <div class="col-md-10">
        <textarea name="description" class="form-control" rows="3" maxlength="255"
            placeholder="{{ __('Description') }}">{{ old('description', $tenant?->description) }}</textarea>
    </div>
</div>

<div class="form-group row">
    <label for="is_default" class="col-md-2 col-form-label">@lang('Default')</label>

    <div class="col-md-10">
        <div class="form-check">
            <input type="checkbox" name="is_default" id="is_default" value="1" class="form-check-input"
                {{ old('is_default', $tenant?->is_default) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_default">
                @lang('Set as default tenant')
            </label>
        </div>
    </div>
</div>
