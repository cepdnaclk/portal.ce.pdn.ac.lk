@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy Page'))

@section('content')
    <div x-data="{
        metadata: {{ json_encode($taxonomyPage->metadata) ?: '{}' }},
    }">
        {!! Form::model($taxonomyPage, [
            'url' => route('dashboard.taxonomy-pages.update', $taxonomyPage->id),
            'method' => 'PUT',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        @csrf

        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy Page : Edit') }}
            </x-slot>

            <!-- Body -->
            <x-slot name="body">
                <div class="card mb-4">
                    <div class="card-body">

                        <!-- Slug -->
                        <div class="row">
                            {!! Form::label('slug', 'Slug*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::text('slug', $taxonomyPage->slug, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'max_length' => 255,
                                    'placeholder' => 'Enter an unique slug (e.g., about-us) as the page identifier',
                                ]) !!}
                                @error('slug')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            {!! Form::label('html', 'HTML Content*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <livewire:backend.richtext-editor-component name="html"
                                    value="{{ old('html', $taxonomyPage->html ?? '') }}" style="height: 300px;" />
                                @error('html')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        <!-- Tenant -->
                        <div class="row">
                            {!! Form::label('tenant_id', 'Tenant*', ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
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
                        </div>

                        <!-- Taxonomy Selector -->
                        @isset($taxonomies)
                            <div class="row">
                                {!! Form::label('taxonomy_id', 'Related Taxonomy (Optional)', ['class' => 'col-form-label']) !!}
                                <a href="{}"></a>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <select name="taxonomy_id" id="taxonomy_id" class="form-control">
                                        <option value="">{{ __('— none —') }}</option>
                                        @foreach ($taxonomies as $taxonomy)
                                            <option value="{{ $taxonomy->id }}" data-tenant="{{ $taxonomy->tenant_id }}"
                                                {{ old('taxonomy_id', $taxonomyPage->taxonomy_id) == $taxonomy->id ? 'selected' : '' }}>
                                                {{ $taxonomy->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('taxonomy_id')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>
                        @endisset
                    </div>
                </div>
            </x-slot>

            <!-- Footer -->
            <x-slot name="footer">
                {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
            </x-slot>
        </x-backend.card>

        {!! Form::close() !!}
    </div>

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
