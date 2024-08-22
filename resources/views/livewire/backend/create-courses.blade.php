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
                        <p class="card-text">This is the first step of the form.</p>
                    </div>
                </div>
            </div>
            @elseif ($formStep == 2)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">ILOs & Objectives</h5>
                            <p class="card-text">This is the second step of the form.</p>
                        </div>
                    </div>
                </div>
            @elseif ($formStep == 3)
                <div class="step active">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Modules & References</h5>
                            <h6>References</h6>
                            <x-backend.addItem/>
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
