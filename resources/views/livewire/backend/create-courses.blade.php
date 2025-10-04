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

                                    {{-- Academic Program --}}
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label for="dropAcademicProgram">
                                                Academic Program*
                                            </label>
                                        </div>
                                        <select id="dropAcademicProgram" name="dropAcademicProgram" class="form-select"
                                            wire:model="academicProgram">
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

                                    {{-- Curriculum --}}
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label for="dropCurriculum">Curriculum*</label>

                                            <x-backend.taxonomy_tooltip
                                                edit-url="{{ route('dashboard.taxonomy.term.alias', ['code' => 'academic_program']) }}"
                                                placement="auto" class="float-end">
                                            </x-backend.taxonomy_tooltip>
                                        </div>
                                        <select id="dropCurriculum" name="dropCurriculum" class="form-select"
                                            wire:model="version">
                                            <option style="display:none" selected></option>
                                            @foreach ($curriculumList as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('version')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Semester --}}
                                    <div class="col-12 py-2">
                                        <div class="col ps-0">
                                            <label for="drop1">Semester*</label>
                                        </div>
                                        <select id="dropSemester" name="dropSemester" class="form-select"
                                            wire:model="semester">
                                            <option style="display:none" selected></option>
                                            @foreach ($semestersList as $semesterId => $semesterTitle)
                                                <option value="{{ $semesterId }}">{{ $semesterTitle }}</option>
                                            @endforeach
                                        </select>
                                        @error('semester')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Course Code --}}
                                    <div class="col-12 col-sm-3 py-2">
                                        <div class="col ps-0">
                                            <label>Code*</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control" wire:model.lazy = "code">
                                        </div>
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Course Name --}}
                                    <div class="col-12 col-sm-9 py-2">
                                        <div class="col ps-0">
                                            <label>Name*</label>
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
                                    {{-- Course Type --}}
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label for="dropType">Type*</label>
                                        </div>
                                        <select id="dropType" name="dropType" class="form-select" wire:model="type">
                                            <option style="display:none" selected></option>
                                            @foreach (App\Domains\AcademicProgram\Course\Models\Course::getTypes() as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Credits --}}
                                    <div class="col-12 col-sm-6 py-2">
                                        <div class="col ps-0">
                                            <label>Credits*</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="number" class="form-control" wire:model.lazy ="credits">
                                        </div>
                                        @error('credits')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Teaching Methods --}}
                                    <div class="col-12 py-2">
                                        <div class="col ps-0">
                                            <label>Teaching Methods</label>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                wire:model.lazy = "teaching_methods">
                                        </div>
                                        @error('teaching_methods')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- FAQ Page --}}
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

                                    {{-- Content --}}
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
                            <label for="floatingTextarea">Aims/Objectives</label>
                            @error('objectives')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Prerequisites --}}
                    <div class="h4 font-weight-bold mt-5">
                        Prerequisites:
                        <hr>
                    </div>
                    @livewire('backend.prerequisite-selector', ['academic_program' => $academicProgram, 'version' => $version, 'semester' => $semester, 'prerequisites' => $prerequisites])
                    <div class="h4 font-weight-bold">
                        ILOs:
                        <hr>
                    </div>

                    {{-- ILOs --}}
                    @foreach ($ilos as $key => $value)
                        <div class="mt-3">
                            @livewire('backend.item-adder', ['type' => $key, 'title' => $key, 'items' => $ilos[$key]], key("ilos-$key-adder"))
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    @elseif ($formStep == 3)
        <div class="step active">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Modules & References</h5>

                    {{-- Modules --}}
                    <div class="pb-5">
                        <x-backend.course_module></x-backend.course_module>
                    </div>

                    {{-- References --}}
                    <div class="pb-5">
                        @livewire('backend.item-adder', ['type' => 'references', 'title' => 'References / Recommended Reading', 'items' => $references], key('references-adder'))
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
