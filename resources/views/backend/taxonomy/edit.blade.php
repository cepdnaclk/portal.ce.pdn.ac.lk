@extends('backend.layouts.app')

@section('content')
    <div>
        {{-- {!! Form::open([
            'url' => route('dashboard.event.store'),
            'method' => 'post',
            'class' => 'container',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!} --}}

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
                        <select class="form-select" wire:model="semester">
                            <option style="display:none" selected></option>
                            {{-- @foreach ($semestersList as $semesterId => $semesterTitle)
                                <option value="{{ $semesterId }}">{{ $semesterTitle }}</option>
                            @endforeach --}}
                        </select>
                        {{-- @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror --}}
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Taxonomy*</label>
                        </div>
                        <select class="form-select" wire:model="semester">
                            <option style="display:none" selected></option>
                            {{-- @foreach ($semestersList as $semesterId => $semesterTitle)
                                <option value="{{ $semesterId }}">{{ $semesterTitle }}</option>
                            @endforeach --}}
                        </select>
                        {{-- @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror --}}
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Taxonomy Term Code*</label>
                        </div>
                        <div class="col-md-12 px-0">
                            {!! Form::text('tax_term_code', '', ['class' => 'form-control']) !!}
                            {{-- @error('title')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror --}}
                        </div>
                        {{-- @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror --}}
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Taxonomy Term Name*</label>
                        </div>
                        <div class="col-md-12 px-0">
                            {!! Form::text('tax_term_name', '', ['class' => 'form-control']) !!}
                            {{-- @error('title')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror --}}
                        </div>
                        {{-- @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror --}}
                    </div>
                </div>

                <div class="metadata py-3 mt-5 mb-3" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">

                    <div class="col-12 pb-3">
                        <strong>Metadata</strong>
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Country</label>
                        </div>
                        <div class="col-md-12 px-0">
                            {!! Form::text('country', '', ['class' => 'form-control']) !!}
                            {{-- @error('title')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror --}}
                        </div>
                        {{-- @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror --}}
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Country Code</label>
                        </div>
                        <div class="col-md-12 px-0">
                            {!! Form::text('country_code', '', ['class' => 'form-control']) !!}
                            {{-- @error('title')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror --}}
                        </div>
                        {{-- @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror --}}
                    </div>

                    <div class="col-12 py-2">
                        <div class="col ps-0">
                            <label for="drop1">Page</label>
                        </div>
                        <div class="col-md-12 px-0">
                            {!! Form::text('page', '', ['class' => 'form-control']) !!}
                            {{-- @error('title')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror --}}
                        </div>
                        {{-- @error('semester')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror --}}
                    </div>

                    <div class="col-12 py-2">
                        {!! Form::label('enabled', 'Enabled', ['class' => 'col-ps-0 pb-2 form-check-label']) !!}
    
                        <div class="col form-check form-switch mx-2">
                            <input type="checkbox" id="checkEnable" name="enabled" value="1"
                                class="form-check-input checkbox-lg" checked />
                            <label class="form-check-label" for="checkEnable">&nbsp;</label>
                            {{-- @error('enabled')
                                <strong class="text-danger">{{ $message }}</strong>
                            @enderror --}}
                        </div>
                    </div>

                    <div class="col-12 py-2">
                        {!! Form::label('independence_date', 'Independence Date', ['class' => 'col-ps-0 pb-2 col-form-label']) !!}
                        <div class="col-12 px-0">
                            {!! Form::datetimeLocal('indepen_day','', ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>

            </x-slot>

            <x-slot name="footer">
                {!! Form::submit('Update', ['class' => 'btn btn-primary btn-w-150 float-right', 'id' => 'submit-button']) !!}
            </x-slot>

        </x-backend.card>

        {{-- {!! Form::close() !!} --}}
    </div>

@endsection
