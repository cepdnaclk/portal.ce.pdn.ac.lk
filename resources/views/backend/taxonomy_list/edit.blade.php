@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy List'))

@section('content')
    {!! Form::model($taxonomyList, [
        'url' => route('dashboard.taxonomy-lists.update', $taxonomyList),
        'method' => 'PUT',
        'class' => 'container',
    ]) !!}

    @csrf

    <x-backend.card>
        <x-slot name="header">
            Taxonomy List : Edit
        </x-slot>

        <x-slot name="body">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title" style="text-align: left; text-decoration: none;">Basic Configurations</h5>

                    <div class="mb-3">
                        {!! Form::label('name', 'Name*', ['class' => 'form-label']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required' => true]) !!}
                        @error('name')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>

                    <div class="mb-3">
                        {!! Form::label('tenant_id', 'Tenant*', ['class' => 'form-label']) !!}
                        {!! Form::select('tenant_id', $tenants->pluck('name', 'id'), $selectedTenantId, [
                            'class' => 'form-select',
                            'required' => true,
                            'placeholder' => '',
                            'id' => 'tenant_id',
                        ]) !!}
                        @error('tenant_id')
                            <strong class="text-danger">{{ $message }}</strong>
                        @enderror
                    </div>

                    @isset($taxonomies)
                        <div class="mb-3">
                            {!! Form::label('taxonomy_id', 'Related Taxonomy (Optional)', ['class' => 'form-label']) !!}
                            <select name="taxonomy_id" id="taxonomy_id" class="form-select">
                                <option value="">{{ __('— none —') }}</option>
                                @foreach ($taxonomies as $taxonomy)
                                    <option value="{{ $taxonomy->id }}" data-tenant="{{ $taxonomy->tenant_id }}"
                                        {{ old('taxonomy_id', $taxonomyList->taxonomy_id) == $taxonomy->id ? 'selected' : '' }}>
                                        {{ $taxonomy->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('taxonomy_id')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror
                        </div>
                    @endisset

                    <div class="mb-3">
                        {!! Form::label('data_type', 'Data Type*', ['class' => 'form-label']) !!}

                        @if (count($taxonomyList->items ?? []) > 0)
                            {!! Form::text('data_type', null, ['class' => 'form-control', 'readonly' => true]) !!}

                            <small class="text-muted">
                                {{ __('Data type cannot be changed while the list contains items.') }}
                            </small>
                        @else
                            {!! Form::select('data_type', $dataTypes, $taxonomyList->data_type, [
                                'class' => 'form-select',
                                'required' => true,
                                'disabled' => count($taxonomyList->items ?? []) > 0 ? 'disabled' : null,
                            ]) !!}
                        @endif

                        @error('data_type')
                            <strong class="text-danger d-block">{{ $message }}</strong>
                        @enderror
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
            <a href="{{ route('dashboard.taxonomy-lists.index') }}" class="btn btn-light float-end btn-w-150 me-2">Back</a>
        </x-slot>
    </x-backend.card>

    {!! Form::close() !!}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tenantSelect = document.getElementById('tenant_id')
            const taxonomySelect = document.getElementById('taxonomy_id')
            if (!tenantSelect || !taxonomySelect) {
                return
            }
            const syncTaxonomyOptions = () => {
                const tenantId = tenantSelect.value
                Array.from(taxonomySelect.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false
                        return
                    }
                    const matchesTenant = !tenantId || option.dataset.tenant === tenantId
                    option.hidden = !matchesTenant
                    if (!matchesTenant && option.selected) {
                        option.selected = false
                    }
                })
            }
            tenantSelect.addEventListener('change', syncTaxonomyOptions)
            syncTaxonomyOptions()
        })
    </script>
@endsection
