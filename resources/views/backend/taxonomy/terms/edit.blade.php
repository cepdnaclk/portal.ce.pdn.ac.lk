@extends('backend.layouts.app')

@section('content')
    <div>

        <x-backend.card>

            <x-slot name="body">

                <div class="term py-2 pt-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">

                    <div class="col-12 pb-3">
                        <strong>Term Configurations</strong>
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Parent Taxonomy Term</label>
                        </div>
                        <select class="form-select">
                            <option style="display:none" selected></option>
                        </select>
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Taxonomy*</label>
                        </div>
                        <select class="form-select">
                            <option style="display:none" selected></option>
                        </select>
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Taxonomy Term Code*</label>
                        </div>
                        <div class="col-md-12 px-0">
                            {!! Form::text('tax_term_code', '', ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Taxonomy Term Name*</label>
                        </div>
                        <div class="col-md-12 px-0">
                            {!! Form::text('tax_term_name', '', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="metadata py-3 mt-5 mb-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">

                    <div class="col-12 pb-3">
                        <strong>Metadata</strong>
                    </div>

                    @foreach(json_decode($taxonomy->properties, true) as $property)
                        <div class="col-12 py-2">
                            <div class="col ps-0">
                                <label >{{ $property['name'] }}</label>
                            </div>
                            <div class="col-md-12 px-0">
                                @switch($property['data_type'])
                                    @case('string')
                                        {!! Form::text("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                        @break
                                    @case('number')
                                        {!! Form::number("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                        @break
                                    @case('boolean')
                                        <div class="form-check">
                                            {!! Form::checkbox("metadata[{$property['code']}]", 1, null, ['class' => 'form-check-input', 'id' => $property['code']]) !!}
                                        </div>
                                        @break
                                    @case('date')
                                        {!! Form::date("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                        @break
                                    @default
                                        {!! Form::text("metadata[{$property['code']}]", null, ['class' => 'form-control', 'id' => $property['code']]) !!}
                                @endswitch
                            </div>
                        </div>
                    @endforeach

            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>

    </div>

@endsection
