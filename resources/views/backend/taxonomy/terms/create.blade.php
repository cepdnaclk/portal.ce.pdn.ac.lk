<?php use App\Domains\Taxonomy\Models\TaxonomyTerm; ?>

@extends('backend.layouts.app')

@section('title', __('Create Taxonomy Term'))

@section('content')
    <div>
        @if ($taxonomy && $taxonomy->description)
            <livewire:backend.expandable-info-card :title="'Taxonomy: ' . $taxonomy->name" :description="$taxonomy->description" />
        @endif

        <x-backend.card>
            <x-slot name="body">
                <form method="POST" action="{{ route('dashboard.taxonomy.terms.store', $taxonomy) }}">
                    @csrf

                    <div class="term py-2 pt-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">

                        <div class="col-12 pb-3">
                            <strong>Term Configurations</strong>
                        </div>

                        <!-- Taxonomy -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="drop1">Taxonomy*</label>
                            </div>

                            <div class="col-md-12 px-0">
                                {!! Form::text('taxonomy_name', $taxonomy->name, ['class' => 'form-control', 'readonly']) !!}
                                {!! Form::hidden('taxonomy_id', $taxonomy->id, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                        </div>

                        <!-- Parent Taxonomy Term -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="drop1">Parent Taxonomy Term ( Optional)</label>
                            </div>
                            <select name="parent_id" class="form-select">
                                <option value="" selected>Select</option>
                                @foreach ($taxonomy->terms as $sibling)
                                    <option value="{{ $sibling->id }}">
                                        {{ TaxonomyTerm::getHierarchicalPath($sibling->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Taxonomy Term Name -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="drop1">Taxonomy Term Name*</label>
                            </div>
                            <div class="col-md-12 px-0">
                                {!! Form::text('name', '', ['class' => 'form-control', 'id' => 'name-input']) !!}
                            </div>
                        </div>

                        <!-- Taxonomy Term Code -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="drop1">Taxonomy Term Code*</label>
                            </div>
                            <div class="col-md-12 px-0">
                                {!! Form::text('code', '', ['class' => 'form-control', 'id' => 'code-input']) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Metadata Section -->
                    <div class="metadata py-3 mt-5 mb-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">
                        <div class="col-12 pb-3">
                            <strong>Metadata</strong>
                        </div>

                        @if (empty($taxonomy->properties))
                            <div class="col-12">
                                <p class="text-muted">No metadata properties available for this taxonomy.</p>
                            </div>
                        @endif

                        @foreach ($taxonomy->properties as $property)
                            <livewire:backend.taxonomy-term-metadata :property="$property" :taxonomy="$taxonomy" />
                        @endforeach
                    </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary btn-w-150 float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>

    </div>

    <script>
        // JavaScript to auto-generate the code field based on the Taxonomy Term's name input
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name-input');
            const codeInput = document.getElementById('code-input');

            nameInput.addEventListener('input', function() {
                const slug = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-') // Replace non-alphanumeric characters with hyphens
                    .replace(/^-+|-+$/g, ''); // Trim leading and trailing hyphens
                codeInput.value = slug;
            });
        });
    </script>
@endsection
