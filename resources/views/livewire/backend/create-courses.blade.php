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
                                <form action="#">
                                    <div class="row" id="row1">
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Academic Program</label>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    Please select
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Semester Id</label>
                                            </div>
                                            <div class="dropdown-center">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    Please select
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Version</label>
                                            </div>
                                            <div class="dropdown-center">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    Please select
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Type</label>
                                            </div>
                                            <div class="dropdown-center">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    Please select
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                                    <li><a class="dropdown-item" href="#">Something else here</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="row2">
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Code</label>
                                            </div>
                                            <div class="input-group w-75">
                                                <input type="text" class="form-control me-5" placeholder="CO200">
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Name</label>
                                            </div>
                                            <div class="input-group w-75">
                                                <input type="text" class="form-control me-5"
                                                    placeholder="Database Systems">
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">Credits</label>
                                            </div>
                                            <div class="input-group w-75">
                                                <input type="text" class="form-control me-5" placeholder="3">
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3 py-3">
                                            <div class="col">
                                                <label for="drop1">FAQ page</label>
                                            </div>
                                            <div class="input-group w-75">
                                                <input type="text" class="form-control me-5"
                                                    placeholder="https://www.url.com">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="my-2" id="contentarea">
                                        <label class="pb-2" for="textarea">Content</label>
                                        <div class="form-floating mb-4">
                                            <textarea id="textarea" class="form-control auto-resize-textarea" id="floatingTextarea"
                                                oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                                        </div>
                                    </div>

                                    <div class="div my-2">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="div text-center">
                                                    <label for="drop1">Time Allocation</label>
                                                </div>
                                                <hr>
                                                <div class="row pb-2">
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>Lectures</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>Tutorial</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row pb-2">
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>Practical</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>Assignment</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="div text-center">
                                                    <label for="drop1">Marks Allocation</label>
                                                </div>
                                                <hr>
                                                <div class="row pb-2">
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>Practicles</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>Project</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row pb-2">
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>Mid-Exam</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                        <div class="col">
                                                            <label>End-exam</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($formStep == 2)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            {{-- <h5 class="card-title">ILOs & Objectives</h5>
                            <p class="card-text">This is the second step of the form.</p> --}}

                            {{-- Objectives --}}
                            <div class="h4 font-weight-bold mt-3">
                                Aims/Objectives:
                                <hr>
                            </div>

                            {{-- objectives --}}
                            <div class="form-group mt-3">
                                <div class="form-floating">
                                    {!! Form::textarea('objectives', '', [
                                        'class' => 'form-control',
                                        'id' => 'floatingTextarea',
                                        'placeholder' => '',
                                        'rows' => 8,
                                        'style' => 'height: 200px;',
                                    ]) !!}
                                    <label for="floatingTextarea">Objectives</label>
                                    @error('objectives')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                            </div>

                            <div class="h4 font-weight-bold mt-5">
                                ILOs:
                                <hr>
                            </div>

                            {{-- Attitude --}}

                            <div class="mt-5">
                                @livewire('backend.item-adder', ['type' => 'knowledge', 'items' => $knowledge], key('ilos--knowledge-adder'))


                            </div>

                            <div class="mt-5">
                                @livewire('backend.item-adder', ['type' => 'skill', 'items' => $skills], key('ilos-skill-adder'))


                            </div>

                            <div class="mt-5">
                                @livewire('backend.item-adder', ['type' => 'attitude', 'items' => $attitudes], key('ilos-attitude-adder'))


                            </div>

                        </div>
                    </div>
                </div>
            @elseif ($formStep == 3)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Modules & References</h5>

                            <div class="container mx-auto mt-6 px-3 py-2">

                                <div
                                x-data="{
                                    modules: [],
                                    addModule() {
                                        this.modules.push({id: Date.now()});
                                    }
                                }">
                                    <div class="px-3 py-3 my-6 rounded mb-4" style="border: 1px solid rgb(209, 209, 209)">

                                        <div>
                                            <div class="mb-3">
                                                <div >
                                                    <label>Name</label>
                                                </div>
                                                <input 
                                                type="text"  
                                                class="form-control border p-2 mb-2  rounded-md">
                                            </div>
                                            <div class="mb-3">
                                                <div>
                                                    <label>Description</label>
                                                </div>
                                                <div class="form-floating mb-4">
                                                    <textarea class="form-control auto-resize-textarea"
                                                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                                                </div>
                                            </div>                                                                                       
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col d-flex align-items-center col-md-3 col-6">
                                                <div class="col">
                                                    <label>Lectures</label>
                                                </div>
                                                <div class="col ms-auto py-3">
                                                    <input class="form-control" type="number">
                                                </div>
                                            </div>
                        
                                            <div class="col d-flex align-items-center col-md-3 col-6">
                                                <div class="col">
                                                    <label>Tutorial</label>
                                                </div>
                                                <div class="col ms-auto py-3">
                                                    <input class="form-control" type="number">
                                                </div>
                                            </div>
                        
                                            <div class="col d-flex align-items-center col-md-3 col-6">
                                                <div class="col">
                                                    <label>Practicles</label>
                                                </div>
                                                <div class="col ms-auto py-3">
                                                    <input class="form-control" type="number">
                                                </div>
                                            </div>
                        
                                            <div class="col d-flex align-items-center col-md-3 col-6">
                                                <div class="col">
                                                    <label>Assignments</label>
                                                </div>
                                                <div class="col ms-auto py-3">
                                                    <input class="form-control" type="number">
                                                </div>
                                            </div>
                        
                                        </div>
                                    </div>
                        
                                    <template x-for="(item, index) in modules" :key="item.id">
                                        <div x-transition x-cloak>
                                            <div class="px-3 py-3 my-6 rounded mb-4" style="border: 1px solid rgb(209, 209, 209)">
                                                <div>
                                                    <div class="mb-3">
                                                        <div >
                                                            <label>Name</label>
                                                        </div>
                                                        <input 
                                                        type="text"  
                                                        class="form-control border p-2 mb-2  rounded-md">
                                                    </div>
                                                    <div class="mb-3">
                                                        <div>
                                                            <label>Description</label>
                                                        </div>
                                                        <div class="form-floating mb-4">
                                                            <textarea class="form-control auto-resize-textarea"
                                                                oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                
                                                    <div class="col d-flex align-items-center col-md-3 col-6">
                                                        <div class="col">
                                                            <label>Lectures</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                
                                                    <div class="col d-flex align-items-center col-md-3 col-6">
                                                        <div class="col">
                                                            <label>Tutorial</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                
                                                    <div class="col d-flex align-items-center col-md-3 col-6">
                                                        <div class="col">
                                                            <label>Practicles</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                
                                                    <div class="col d-flex align-items-center col-md-3 col-6">
                                                        <div class="col">
                                                            <label>Assignments</label>
                                                        </div>
                                                        <div class="col ms-auto py-3">
                                                            <input class="form-control" type="number">
                                                        </div>
                                                    </div>
                                
                                                </div>
                                                <button
                                                x-on:click="modules.splice(index, 1)"
                                                class="btn btn-danger px-4 py-2 rounded-md">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                        
                                    <button  
                                    x-on:click="addModule()"
                                    class="btn btn-primary px-4 py-2 mb-3 rounded-md">
                                        Add Module
                                    </button>
                        
                                </div>
                            </div>

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
