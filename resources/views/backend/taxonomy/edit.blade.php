@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy'))

@section('content')
    <div x-data="{ properties: {{ json_encode($taxonomy->properties) }}, is_editable: '1' }">
        {!! Form::model($taxonomy, [
            'url' => route('dashboard.taxonomy.update', $taxonomy->id),
            'method' => 'PUT',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        @csrf
        <x-backend.card>
            <x-slot name="header">
                Taxonomy : Edit
            </x-slot>

            <x-slot name="body">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: left; text-decoration: none;">Basic Configurations</h5>
                        <!-- Taxonomy Code -->
                        <div class="row">
                            {!! Form::label('code', 'Taxonomy Code*', ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::text('code', null, ['class' => 'form-control']) !!}
                                @error('code')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        <!-- Taxonomy Name -->
                        <div class="row">
                            {!! Form::label('name', 'Taxonomy Name*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                @error('name')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>

                        <!-- Taxonomy Description -->
                        <div class="row">
                            {!! Form::label('description', 'Taxonomy Description*', ['class' => 'col-form-label']) !!}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                {!! Form::textarea('description', null, [
                                    'class' => 'form-control',
                                    'style' => 'overflow:hidden;height: 100px;',
                                    'oninput' => "this.style.height = '100px';this.style.height = this.scrollHeight + 'px';",
                                ]) !!}
                                @error('description')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>
                        </div>
                    </div>
 
                        <!-- Visibility -->
                        <div class="form-group row mt-3">
                            <label for="visibility" class="col-md-2 col-form-label">Visible to public</label>
                            <div class="col-md-2 form-check form-switch mx-4">
                                <input type="checkbox" id="visibility" name="visibility" class="form-check-input checkbox-lg" {{ $taxonomy->visibility ? 'checked' : '' }}>
                                <label class="form-check-label" for="visibility">&nbsp;</label>
                            </div>
                        </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: left; text-decoration: none;">Properties</h5>

                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </symbol>
                            </svg>

                            <div class="alert alert-secondary d-flex align-items-center" role="alert">
                                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                                    aria-label="Warning:">
                                    <use xlink:href="#exclamation-triangle-fill" />
                                </svg>

                                <div>
                                    <b>Edit</b> and <b>Delete</b> options should be carefully used since already have <a
                                        href="{{ route('dashboard.taxonomy.terms.index', $taxonomy) }}">taxonomy
                                        terms</a>.<br>
                                </div>
                            </div>
                        </div>

                        <x-backend.taxonomy_property_adder />
                        {!! Form::hidden('properties', '', ['x-model' => 'JSON.stringify(properties)']) !!}
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-right']) !!}
            </x-slot>

        </x-backend.card>

        {!! Form::close() !!}
    </div>
@endsection
