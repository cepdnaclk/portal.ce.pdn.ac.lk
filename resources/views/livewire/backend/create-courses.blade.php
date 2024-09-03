<x-backend.card>
    <x-slot name="header">
        Course : Create
    </x-slot>

    <x-slot name="body">
        <div class="container mt-1" id="app">
            <div class="step-indicator">
                <div class="step-item @if ($formStep >= 1) active @endif">
                    <span class="step-count">1</span>
                </div>
                <div class="step-item @if ($formStep >= 2) active @endif">
                    <span class="step-count">2</span>
                </div>
                <div class="step-item @if ($formStep >= 3) active @endif">
                    <span class="step-count">3</span>
                </div>
            </div>
            @if ($formStep == 1)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Basics</h5>
                            <div class="basics">
                                    <div class="row" id="row1">
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">
                                                    Academic Program
                                                    <span title="e.g., Undergraduate or Postgraduate" 
                                                          style="cursor: pointer;">&#x1F6C8;</span>
                                                </label>
                                            </div>
                                            <select class="form-select" wire:model="academicProgram">
                                                <option selected>Select</option>
                                                @foreach($academicProgramsList as $academicProgramId => $academicProgramTitle)
                                                    <option value="{{ $academicProgramId }}">{{ $academicProgramTitle }}</option>
                                                @endforeach
                                            </select>
                                            @error('academicProgram') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Version</label>
                                            </div>
                                            <select class="form-select" wire:model="version">
                                                <option selected>Select</option>
                                                @foreach(App\Domains\Course\Models\Course::getVersions() as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>                                                                                     
                                            @error('version') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Semester</label>
                                            </div>
                                            <select class="form-select" wire:model="semester">
                                                <option value="">Select</option>
                                                @foreach($semestersList as $semesterId => $semesterTitle)
                                                    <option value="{{ $semesterId }}">{{ $semesterTitle }}</option>
                                                @endforeach
                                            </select>  
                                            @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror  
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Type</label>
                                            </div>
                                            <select class="form-select" wire:model="type">
                                                <option selected>Select</option>
                                                @foreach(App\Domains\Course\Models\Course::getTypes() as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select> 
                                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror                                           
                                        </div>
                                    </div>

                                    <div class="row" id="row2">
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label>Code</label>
                                            </div>
                                            <div class="input-group w-75">
                                                <input type="text" class="form-control me-5" wire:model.lazy = "code" placeholder="CO200">
                                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label>Name</label>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control me-5" wire:model.lazy = "name"
                                                    placeholder="Database Systems">
                                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-2 py-3">
                                            <div class="col">
                                                <label>Credits</label>
                                            </div>
                                            <div class="input-group w-75">
                                                <input type="text" class="form-control me-5" wire:model.lazy ="credits" placeholder="3">
                                                @error('credits') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-4 py-3">
                                            <div class="col">
                                                <label>FAQ page</label>
                                            </div>
                                            <div class="input-group">
                                                <input type="text" class="form-control me-5" wire:model.lazy = "faq_page"
                                                    placeholder="https://www.url.com">
                                                @error('faq_page') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" id="contentarea">
                                        <label class="pb-2" for="contentTextarea">Content</label>
                                        <textarea class="form-control" id="contentTextarea" wire:model.lazy = "content" rows="3"></textarea>
                                        @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="div my-2">
                                        <div class="row">
                                            <div x-data="{ time_allocation: $wire.entangle('time_allocation') }" class="col-6">
                                                <div class="div text-center">
                                                    <label>Time Allocation</label>
                                                </div>
                                                <hr>
                                                <div class="row pb-2">
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>Lectures</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="time_allocation.lecture" wire:model.defer="time_allocation.lecture">
                                                            @error('time_allocation.lecture') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>Tutorial</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="time_allocation.tutorial" wire:model.defer="time_allocation.tutorial">
                                                            @error('time_allocation.tutorial') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row pb-2">
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>Practical</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="time_allocation.practical" wire:model.defer="time_allocation.practical">
                                                            @error('time_allocation.practical') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>Assignment</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="time_allocation.assignment" wire:model.defer="time_allocation.assignment">
                                                            @error('time_allocation.assignment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                                                                           
                                            <div x-data="{ marks_allocation: $wire.entangle('marks_allocation') }" class="col-6">
                                                <div class="div text-center">
                                                    <label>Marks Allocation</label>
                                                </div>
                                                <hr>
                                                <div class="row pb-2">
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>Practicles</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="marks_allocation.practicals" wire:model.defer="marks_allocation.practicals">
                                                            @error('time_allocation.practicals') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>Project</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="marks_allocation.project" wire:model.defer="marks_allocation.project">
                                                            @error('time_allocation.project') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row pb-2">
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>Mid-Exam</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="marks_allocation.mid_exam" wire:model.defer="marks_allocation.mid_exam">
                                                            @error('time_allocation.mid_exam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row d-flex justify-content-between align-items-center col-md-6">
                                                        <div class="col">
                                                            <label>End-Exam</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number" x-model.number="marks_allocation.end_exam" wire:model.defer="marks_allocation.end_exam">
                                                            @error('time_allocation.end_exam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($formStep == 2)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            {{-- Objectives --}}
                            <div class="h4 font-weight-bold mt-3">
                                Aims/Objectives:
                                <hr>
                            </div>

                            {{-- objectives --}}
                            <div class="form-group mt-3">
                                <div class="form-floating">
                                    {!! Form::textarea('objectives', '', [
                                        'class' => 'form-control' . ($errors->has('objectives') ? ' is-invalid' : ''),
                                        'id' => 'floatingTextarea',
                                        'placeholder' => '',
                                        'rows' => 8,
                                        'style' => 'height: 200px;',
                                        'wire:model.lazy' => 'objectives'
                                    ]) !!}
                                    <label for="floatingTextarea">Objectives</label>
                                    @error('objectives') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="h4 font-weight-bold mt-5">
                                ILOs:
                                <hr>
                            </div>

                            {{-- ILO --}}

                            <div class="mt-5">
                                @livewire('backend.item-adder', ['type' => 'knowledge', 'items' => $ilos['knowledge']], key('ilos-knowledge-adder'))
                            </div>

                            <div class="mt-5">
                                @livewire('backend.item-adder', ['type' => 'skills', 'items' => $ilos['skills']], key('ilos-skill-adder'))
                            </div>

                            <div class="mt-5">
                                @livewire('backend.item-adder', ['type' => 'attitudes', 'items' => $ilos['attitudes']], key('ilos-attitude-adder'))
                            </div>

                        </div>
                    </div>
                </div>
            @elseif ($formStep == 3)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Modules & References</h5>

                            <x-backend.module></x-backend.module>

                            <h6>References</h6>
                            @livewire('backend.item-adder', ['type' => 'references', 'items' => $references], key('references-adder'))
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="navigation">
            <div class="container-fluid">
                <div class="row">
                    <div class="col" style="padding: 0px;">
                        <div class="btn-group" style="float: right;">
                            @if ($formStep == 1)
                                <button type="button" class="btn btn-primary next-step"
                                    wire:click="next">Next</button>
                            @elseif ($formStep == 2)
                                <button type="button" class="btn btn-primary prev-step"
                                    wire:click="previous">Previous</button>
                                <button type="button" class="btn btn-primary next-step"
                                    wire:click="next">Next</button>
                            @elseif ($formStep == 3)
                                <button type="button" class="btn btn-primary prev-step"
                                    wire:click="previous">Previous</button>
                                <button type="button" class="btn btn-primary next-step"
                                    wire:click="submit">Submit</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-backend.card>