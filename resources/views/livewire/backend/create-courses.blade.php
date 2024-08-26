<x-backend.card>
    <x-slot name="header">
        Course : Create
    </x-slot>

    <x-slot name="body">
        <div class="container mt-1" id="app">
            <div class="step-indicator">
                <div class="step-item @if($formStep >= 1) active @endif">
                    <span class="step-count">1</span>
                </div>
                <div class="step-item @if($formStep >= 2) active @endif">
                    <span class="step-count">2</span>
                </div>
                <div class="step-item @if($formStep >= 3) active @endif">
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
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6 col-xl-3 py-3">
                                        <div class="col">
                                            <label for="drop1">Academic Program</label>
                                        </div>
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6 col-xl-3 py-3">
                                        <div class="col">
                                            <label for="drop1">Academic Program</label>
                                        </div>
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6 col-xl-3 py-3">
                                        <div class="col">
                                            <label for="drop1">Academic Program</label>
                                        </div>
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="row2">
                                    <div class="col-6 col-xl-3 py-3">
                                        <div class="col">
                                            <label for="drop1">Academic Program</label>
                                        </div>
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6 col-xl-3 py-3">
                                        <div class="col">
                                            <label for="drop1">Academic Program</label>
                                        </div>
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6 col-xl-3 py-3">
                                        <div class="col">
                                            <label for="drop1">Academic Program</label>
                                        </div>
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-6 col-xl-3 py-3">
                                        <div class="col">
                                            <label for="drop1">Academic Program</label>
                                        </div>
                                        <div class="dropdown-center">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Dropdown button
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="my-2" id="contentarea">
                                    <label class="pb-2" for="textarea">Academic</label>
                                    <div class="form-floating">
                                        <textarea id="textarea" class="form-control auto-resize-textarea" id="floatingTextarea" style="height: 55px;" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
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
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
                                                    </div>
                                                    <div class="col ms-auto py-3">
                                                        <input class="form-control" type="number">
                                                    </div>
                                                </div>
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
                                                    </div>
                                                    <div class="col ms-auto py-3">
                                                        <input class="form-control" type="number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row pb-2">
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
                                                    </div>
                                                    <div class="col ms-auto py-3">
                                                        <input class="form-control" type="number">
                                                    </div>
                                                </div>
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
                                                    </div>
                                                    <div class="col ms-auto py-3">
                                                        <input class="form-control" type="number">
                                                    </div>
                                                </div>
                                            </div>                     
                                        </div>
                                        <div class="col-6">
                                            <div class="div text-center">
                                                <label for="drop1">Time Allocation</label>
                                            </div>
                                            <hr>
                                            <div class="row pb-2">
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
                                                    </div>
                                                    <div class="col ms-auto py-3">
                                                        <input class="form-control" type="number">
                                                    </div>
                                                </div>
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
                                                    </div>
                                                    <div class="col ms-auto py-3">
                                                        <input class="form-control" type="number">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row pb-2">
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
                                                    </div>
                                                    <div class="col ms-auto py-3">
                                                        <input class="form-control" type="number">
                                                    </div>
                                                </div>
                                                <div class="row d-flex justify-content-between align-items-center col-md-6 ">
                                                    <div class="col">
                                                        <label>Lectures</label>
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
                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
                        </div>
                    </div>
                </div>
            </div>
            @elseif ($formStep == 2)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">ILOs & Objectives</h5>
                            <p class="card-text">This is the second step of the form.</p>
                            @livewire('backend.item-adder', ['type' => 'ilos', 'items' => $ilos], key('ilos-adder'))
                        </div>
                    </div>
                </div>
            @elseif ($formStep == 3)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Modules & References</h5>
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
                    <button type="button" class="btn btn-primary next-step" wire:click="next">Next</button>
                  @elseif ($formStep == 2)
                  <button type="button" class="btn btn-primary prev-step" wire:click="previous">Previous</button>
                  <button type="button" class="btn btn-primary next-step" wire:click="next">Next</button>
                  @elseif ($formStep == 3)
                  <button type="button" class="btn btn-primary prev-step" wire:click="previous">Previous</button>
                  <button type="button" class="btn btn-primary next-step" wire:click="submit">Submit</button>
                  @endif
                  </div>
              </div>
          </div>
        </div>
      </div>
    </x-slot>
</x-backend.card>
