@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy'))

@section('content')
    <div x-data="{ properties: {{$taxonomy->properties}} }">
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
                                {!! Form::textarea('description', null, ['class' => 'form-control','style'=>'overflow:hidden;height: 100px;','oninput'=>"this.style.height = '100px';this.style.height = this.scrollHeight + 'px';"]) !!}
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
                        <x-backend.taxonomy_property_adder/>
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
