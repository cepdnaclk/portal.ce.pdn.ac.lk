<div class="form-group row">
    <label for="tenants" class="col-md-2 col-form-label">@lang('Tenants')</label>

    <div class="col-md-10">
        @forelse($tenants as $tenant)
            <div class="mb-2">
                <div class="form-check">
                    <input name="tenants[]" id="tenant_{{ $tenant->id }}" value="{{ $tenant->id }}"
                        class="form-check-input" type="checkbox"
                        {{ (old('tenants') && in_array($tenant->id, old('tenants'), true)) || (isset($usedTenants) && in_array($tenant->id, $usedTenants, true)) ? 'checked' : '' }} />

                    <label class="form-check-label" for="tenant_{{ $tenant->id }}">
                        {{ $tenant->name }}
                        @if ($tenant->description)
                            <span class="text-muted">- {{ $tenant->description }}</span>
                        @endif
                    </label>
                </div>
            </div>
        @empty
            <p class="mb-0"><em>@lang('There are no tenants to choose from.')</em></p>
        @endforelse
    </div>
</div>
