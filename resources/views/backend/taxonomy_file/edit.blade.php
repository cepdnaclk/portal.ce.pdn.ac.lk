@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy File'))

@section('content')
    <div x-data="{
        metadata: {{ json_encode($taxonomyFile->metadata) ?: '{}' }},
    }">
        {!! Form::model($taxonomyFile, [
            'url' => route('dashboard.taxonomy-files.update', $taxonomyFile->id),
            'method' => 'PUT',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        @csrf

        <x-backend.card>
            <x-slot name="header">
                {{ __('Taxonomy File : Edit') }}
            </x-slot>

            <!-- Body -->
            <x-slot name="body">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: left; text-decoration: none;">Basic Configurations</h5>

                        <!-- Taxonomy Name -->
                        <div class="row">
                            {!! Form::label('file_name', 'File Name*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::text('file_name', null, [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'placeholder' => 'Enter the preferred file name to be displayed',
                                ]) !!}
                                @error('file_name')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        <!-- Current File -->
                        <div class="row">
                            {!! Form::label('current_file', 'Current File', ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 d-flex align-items-center">
                                <a href="{{ route('download.taxonomy-file', [
                                    'file_name' => $taxonomyFile->file_name,
                                    'extension' => $taxonomyFile->getFileExtension(),
                                ]) }}"
                                    target="_blank" class="btn btn-outline-secondary btn-sm me-3" style="min-width: 150px;">
                                    <i class="fa fa-download me-2"></i> {{ $taxonomyFile->getFileNameWithExtension() }}
                                </a>
                            </div>
                        </div>

                        <!-- Replace File -->
                        <div class="row mt-3">
                            {!! Form::label('file', "New File* (10 MB max, supports $supportedExtensions only)", [
                                'class' => 'col-form-label',
                            ]) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::file('file', ['class' => 'form-control', 'required' => true]) !!}
                                @error('file')
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
                                                {{ old('taxonomy_id', $taxonomyFile->taxonomy_id) == $taxonomy->id ? 'selected' : '' }}>
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
