<?php use App\Domains\Taxonomy\Models\TaxonomyTerm; ?>

@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy Term'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="body">
                <form method="POST" action="{{ route('dashboard.taxonomy.terms.update', [$taxonomy, $term]) }}">
                    @csrf
                    @method('PUT')

                    <div class="term py-2 pt-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">
                        <div class="col-12 pb-3">
                            <strong>Term Configurations</strong>
                        </div>

                        <!-- Taxonomy -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="taxonomy">Taxonomy</label>
                            </div>
                            <div class="col-md-12 px-0">
                                {!! Form::text('taxonomy_name', $taxonomy->name, ['class' => 'form-control', 'readonly']) !!}
                                {!! Form::hidden('taxonomy_id', $taxonomy->id, ['class' => 'form-control', 'readonly']) !!}
                            </div>
                        </div>

                        <!-- Parent Taxonomy Term -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="parent_term">Parent Taxonomy Term (Optional)</label>
                            </div>
                            <select name="parent_id" class="form-select">
                                <option value="">Select</option>
                                @foreach ($term->taxonomy->terms as $sibling)
                                    @if ($sibling->id != $term->id)
                                        <option value="{{ $sibling->id }}"
                                            {{ old('parent_id', $term->parent_id) == $sibling->id ? 'selected' : '' }}>
                                            {{ TaxonomyTerm::getHierarchicalPath($sibling->id) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Taxonomy Term Code -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="tax_term_code">Taxonomy Term Code*</label>
                            </div>
                            <div class="col-md-12 px-0">
                                {!! Form::text('code', old('code', $term->code), ['class' => 'form-control']) !!}
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Taxonomy Term Name -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="tax_term_name">Taxonomy Term Name*</label>
                            </div>
                            <div class="col-md-12 px-0">
                                {!! Form::text('name', old('name', $term->name), ['class' => 'form-control']) !!}
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Metadata Section -->
                    <div class="metadata py-3 mt-5 mb-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">
                        <div class="col-12 pb-3">
                            <strong>Metadata</strong>
                        </div>

                        @foreach ($taxonomy->properties as $property)
                            <livewire:backend.taxonomy-term-metadata :property="$property" :term="$term" />
                        @endforeach
                    </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>

    </div>
@endsection
