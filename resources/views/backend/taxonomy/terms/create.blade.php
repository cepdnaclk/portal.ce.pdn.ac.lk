<?php use App\Domains\Taxonomy\Models\TaxonomyTerm; ?>

@extends('backend.layouts.app')

@section('title', __('Create Taxonomy Term'))

@section('content')
    <div>

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

                        <!-- Taxonomy Term Code -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="drop1">Taxonomy Term Code*</label>
                            </div>
                            <div class="col-md-12 px-0">
                                {!! Form::text('code', '', ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <!-- Taxonomy Term Name -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="drop1">Taxonomy Term Name*</label>
                            </div>
                            <div class="col-md-12 px-0">
                                {!! Form::text('name', '', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Metadata Section -->
                    <div class="metadata py-3 mt-5 mb-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">

                        <div class="col-12 pb-3">
                            <strong>Metadata</strong>
                        </div>

                        @foreach ($taxonomy->properties as $property)
                            <div class="col-12 py-2">
                                <div class="col ps-0">
                                    <label>{{ $property['name'] }}
                                        ({{ \App\Domains\Taxonomy\Models\Taxonomy::$propertyType[$property['data_type']] }})
                                    </label>
                                </div>
                                <div class="col-md-12 px-0">
                                    @switch($property['data_type'])
                                        @case('string')
                                            {!! Form::text("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                        @break

                                        @case('integer')
                                            {!! Form::number("metadata[{$property['code']}]", null, [
                                                'class' => 'form-control',
                                                'id' => $property['code'],
                                                'step' => '1',
                                            ]) !!}
                                        @break

                                        @case('float')
                                            {!! Form::number("metadata[{$property['code']}]", null, [
                                                'class' => 'form-control',
                                                'id' => $property['code'],
                                                'step' => 'any',
                                            ]) !!}
                                        @break

                                        @case('boolean')
                                            <div class="form-check">
                                                {!! Form::checkbox("metadata[{$property['code']}]", 1, null, [
                                                    'class' => 'form-check-input',
                                                    'id' => $property['code'],
                                                ]) !!}
                                            </div>
                                        @break

                                        @case('date')
                                            {!! Form::date("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                        @break

                                        @case('datetime')
                                            {!! Form::datetimeLocal("metadata[{$property['code']}]", null, [
                                                'class' => 'form-control',
                                                'id' => $property['code'],
                                            ]) !!}
                                        @break

                                        @case('url')
                                            {!! Form::url("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                        @break

                                        @case('image')
                                            {!! Form::file("metadata[{$property['code']}]", ['class' => 'form-control', 'id' => $property['code']]) !!}
                                        @break

                                        @default
                                            {!! Form::text("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                    </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary btn-w-150 float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>

    </div>
@endsection
