@extends('backend.layouts.app')

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

                        <!-- Parent Taxonomy Term -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="parent_term">Parent Taxonomy Term (Optional)</label>
                            </div>
                            <select name="parent_id" class="form-select">
                                <option value="" selected>Select</option>
                                @foreach($parentTerms as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $term->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Taxonomy -->
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label for="taxonomy">Taxonomy*</label>
                            </div>
                            <select name="taxonomy_id" class="form-select" disabled>
                                <option value="{{ $taxonomy->id }}" selected>{{ $taxonomy->name }}</option>
                            </select>
                            <input type="hidden" name="taxonomy_id" value="{{ $taxonomy->id }}">
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

                        @foreach(json_decode($taxonomy->properties, true) as $property)
                            <div class="col-12 py-2">
                                <div class="col ps-0">
                                    <label>{{ $property['name'] }} ({{ \App\Domains\Taxonomy\Models\Taxonomy::$propertyType[$property['data_type']] }})</label>
                                </div>
                                <div class="col-md-12 px-0">
                                    @switch($property['data_type'])
                                        @case('string')
                                            {!! Form::text("metadata[{$property['code']}]", old("metadata.{$property['code']}", $term->getMetadata($property['code'])), ['class' => 'form-control']) !!}
                                            @break
                                        @case('integer')
                                            {!! Form::number("metadata[{$property['code']}]", old("metadata.{$property['code']}", $term->getMetadata($property['code'])), ['class' => 'form-control', 'step' => '1']) !!}
                                            @break
                                        @case('float')
                                            {!! Form::number("metadata[{$property['code']}]", old("metadata.{$property['code']}", $term->getMetadata($property['code'])), ['class' => 'form-control', 'step' => 'any']) !!}
                                            @break
                                        @case('boolean')
                                            <div class="form-check">
                                                {!! Form::checkbox("metadata[{$property['code']}]", 1, old("metadata.{$property['code']}", $term->getMetadata($property['code']) == 1 ? true : false), ['class' => 'form-check-input']) !!}
                                            </div>
                                            @break
                                        @case('date')
                                            {!! Form::date("metadata[{$property['code']}]", old("metadata.{$property['code']}", $term->getMetadata($property['code'])), ['class' => 'form-control']) !!}
                                            @break
                                        @case('datetime')
                                            {!! Form::datetimeLocal("metadata[{$property['code']}]", old("metadata.{$property['code']}", $term->getMetadata($property['code'])), ['class' => 'form-control']) !!}
                                            @break
                                        @case('url')
                                            {!! Form::url("metadata[{$property['code']}]", old("metadata.{$property['code']}", $term->getMetadata($property['code'])), ['class' => 'form-control']) !!}
                                            @break
                                        @case('image')
                                            {!! Form::file("metadata[{$property['code']}]", ['class' => 'form-control']) !!}
                                            @if($term->getMetadata($property['code']))
                                                <small>Current: {{ $term->getMetadata($property['code']) }}</small>
                                            @endif
                                            @break
                                        @default
                                            {!! Form::text("metadata[{$property['code']}]", old("metadata.{$property['code']}", $term->getMetadata($property['code'])), ['class' => 'form-control']) !!}
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                    </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>

    </div>

@endsection
