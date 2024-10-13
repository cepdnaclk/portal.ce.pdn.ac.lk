@extends('backend.layouts.app')

@section('title', __('Create Taxonomy'))

@section('content')
    <div x-data="{
        properties: [],
        updateProperties(event) {
            this.properties = event.detail;
            $refs.propertiesInput.value = JSON.stringify(this.properties);
        }
    }" x-on:update-properties="updateProperties">
        {!! Form::open([
            'url' => route('dashboard.taxonomy.store'),
            'method' => 'post',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}

        @csrf
        <x-backend.card>
            <x-slot name="header">
                Taxonomy : Create
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
                                {!! Form::text('code', '', ['class' => 'form-control']) !!}
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
                                {!! Form::text('name', '', ['class' => 'form-control']) !!}
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
                                {!! Form::textarea('description', '', ['class' => 'form-control','style'=>'overflow:hidden;height: 100px;','oninput'=>"this.style.height = '100px';this.style.height = this.scrollHeight + 'px';"]) !!}
                                @error('description')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: left; text-decoration: none;">Properties</h5>
                        <x-backend.taxonomy_property_adder></x-backend.taxonomy_property_adder>
                        {!! Form::hidden('properties', '', ['x-ref'=>'propertiesInput']) !!}
                    </div>
                </div>
                

            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Create', ['class' => 'btn btn-primary btn-w-150 float-right']) !!}
            </x-slot>

        </x-backend.card>

        {!! Form::close() !!}
    </div>
@endsection