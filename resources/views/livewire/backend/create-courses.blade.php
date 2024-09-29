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
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label for="drop1">
                                                Academic Program
                                            </label>
                                        </div>
                                        <select class="form-select" wire:model="academicProgram">
                                            <option style="display:none" selected></option>
                                            @foreach ($academicProgramsList as $academicProgramId => $academicProgramTitle)
                                                <option value="{{ $academicProgramId }}">{{ $academicProgramTitle }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academicProgram')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label for="drop1">Curriculum</label>
                                        </div>
                                        <select class="form-select" wire:model="version">
                                            <option style="display:none" selected></option>
                                            @foreach (App\Domains\AcademicProgram\Course\Models\Course::getVersions() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('version')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 py-2">
                                        <div class="col ps-0">
                                            <label for="drop1">Semester</label>
                                        </div>
                                        <select class="form-select" wire:model="semester">
                                            <option style="display:none" selected></option>
                                            @foreach ($semestersList as $semesterId => $semesterTitle)
                                                <option value="{{ $semesterId }}">{{ $semesterTitle }}</option>
                                            @endforeach
                                        </select>
                                        @error('semester')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-3 py-2">
                                        <div class="col ps-0">
                                            <label>Code</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control" wire:model.lazy = "code">
                                        </div>
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-9 py-2">
                                        <div class="col ps-0">
                                            <label>Name</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control" wire:model.lazy = "name">
                                        </div>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row" id="row2">
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label for="drop1">Type</label>
                                        </div>
                                        <select class="form-select" wire:model="type">
                                            <option style="display:none" selected></option>
                                            @foreach (App\Domains\AcademicProgram\Course\Models\Course::getTypes() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label>Credits</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control" wire:model.lazy ="credits">
                                        </div>
                                        @error('credits')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 py-2">
                                        <div class="col ps-0">
                                            <label>FAQ page</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control" wire:model.lazy = "faq_page"
                                                placeholder="https://faq.ce.pdn.ac.lk/academics/">
                                        </div>
                                        @error('faq_page')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="my-2" id="contentarea">
                                        <label for="contentTextarea">Content</label>
                                        <textarea class="form-control" id="contentTextarea" wire:model.lazy = "content" rows="3"></textarea>
                                        @error('content')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="div my-3">
                                    <div class="row">
                                        <x-backend.time_allocation></x-backend.time_allocation>
                                        <x-backend.marks_allocation></x-backend.marks_allocation>
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
                                'wire:model.lazy' => 'objectives',
                            ]) !!}
                            <label for="floatingTextarea">Objectives</label>
                            @error('objectives')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="h4 font-weight-bold">
                        ILOs:
                        <hr>
                    </div>

                    {{-- ILO --}}
                    <div class="pb-4">
                        @livewire('backend.item-adder', ['type' => 'knowledge', 'items' => $ilos['knowledge']], key('ilos-knowledge-adder'))
                    </div>

                    <div class="pb-4">
                        @livewire('backend.item-adder', ['type' => 'skills', 'items' => $ilos['skills']], key('ilos-skill-adder'))
                    </div>

                    <div class="pb-4">
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

                    <div class="pb-5">
                        <x-backend.course_module></x-backend.course_module>
                    </div>

                    <div class="pb-5">
                        @livewire('backend.item-adder', ['type' => 'references', 'items' => $references], key('references-adder'))
                    </div>

                </div>
            </div>
        </div>
        @endif
    </x-slot>

    <x-slot name="footer">
        <div class="navigation">
            <div class="container-fluid">
                <div class="row">
                    <div class="col p-3">
                        <div class="float-end">
                            @if ($formStep == 1)
                                <button type="button" class="btn btn-primary btn-w-150 me-2 next-step"
                                    wire:click="next">Next</button>
                            @elseif ($formStep == 2)
                                <button type="button" class="btn btn-primary btn-w-150 me-2 prev-step"
                                    wire:click="previous">Previous</button>
                                <button type="button" class="btn btn-primary btn-w-150 me-2 next-step"
                                    wire:click="next">Next</button>
                            @elseif ($formStep == 3)
                                <button type="button" class="btn btn-primary btn-w-150 me-2 prev-step"
                                    wire:click="previous">Previous</button>
                                <button type="button" class="btn btn-primary btn-w-150 me-2 next-step"
                                    wire:click="submit">Submit</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-backend.card>
