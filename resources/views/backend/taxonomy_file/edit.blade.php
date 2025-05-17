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

                        <!-- Existing File -->
                        <div class="row">
                            {!! Form::label('current_file', __('Current File'), ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 d-flex align-items-center">
                                <a href="{{ route('dashboard.taxonomy-files.download', $taxonomyFile) }}" target="_blank"
                                    class="btn btn-outline-secondary btn-sm me-3">
                                    <i class="fa fa-download"></i> {{ $taxonomyFile->file_name }}
                                </a>

                                <span class="text-muted">
                                    ({{ number_format($taxonomyFile->size / 1024, 1) }} KB)
                                </span>
                            </div>
                        </div>

                        <!-- Replace File -->
                        <div class="row mt-3">
                            {!! Form::label('file', __('Replace File'), ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::file('file', ['class' => 'form-control']) !!}
                                @error('file')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        <!-- Taxonomy Selector -->
                        @isset($taxonomies)
                            <div class="row">
                                {!! Form::label('taxonomy_id', __('Associate with Taxonomy'), ['class' => 'col-form-label']) !!}
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    {!! Form::select(
                                        'taxonomy_id',
                                        $taxonomies->pluck('name', 'id')->prepend(__('— none —'), ''),
                                        $taxonomyFile->taxonomy_id,
                                        ['class' => 'form-control'],
                                    ) !!}
                                    @error('taxonomy_id')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>
                        @endisset
                    </div>
                </div>

                {{-- ───────────── Metadata ───────────── --}}
                {{-- TODO Use taxonomy property like UOI --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: left; text-decoration: none;">Metadata</h5>

                        <div class="row">
                            {!! Form::label('metadata', __('Metadata JSON'), ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::textarea(
                                    'metadata',
                                    json_encode($taxonomyFile->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                                    [
                                        'class' => 'form-control',
                                        'style' => 'overflow:hidden;height: 120px;',
                                        'oninput' => "this.style.height='120px';this.style.height=this.scrollHeight+'px';",
                                    ],
                                ) !!}
                                @error('metadata')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </x-slot>

            <!-- Footer -->
            <x-slot name="footer">
                {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-w-150 float-right']) !!}
            </x-slot>
        </x-backend.card>

        {!! Form::close() !!}
    </div>
@endsection
