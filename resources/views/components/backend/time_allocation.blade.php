<div class="col-md-6" x-data="{ 
        time_allocation: $wire.entangle('time_allocation'),
        isValidInput(key) {
            const value = Number(this.time_allocation[key]);
            // If the value is null, undefined, or an empty string, consider it valid
            return value === null || value === undefined || value === '' || (value >= 0 && Number.isInteger(value));
        }
        ,
        calculateTotal() {
            return Object.keys(this.time_allocation)
                .reduce((sum, key) => {
                    return this.isValidInput(key) ? sum + (Number(this.time_allocation[key]) || 0) : sum;
                }, 0);
        }

        }">
  <div class=" py-2 px-3 my-2" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">
      <div class="div pt-3">
          <label for="drop1">Time Allocation</label>
      </div>
      <hr>
      <div class="row pb-2">
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Lectures</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" :class="!isValidInput('lecture') ? 'is-invalid' : ''" placeholder="3"  aria-describedby="lectures" x-model.number="time_allocation.lecture" wire:model.defer="time_allocation.lecture" min="0">
                  <span class="input-group-text" id="lectures">hours</span>
                  <div class="invalid-feedback">
                    Value must be a positive integer
                  </div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Tutorials</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" :class="!isValidInput('tutorial') ? 'is-invalid' : ''" placeholder="3"  aria-describedby="tutorials" x-model.number="time_allocation.tutorial" wire:model.defer="time_allocation.tutorial" min="0">
                  <span class="input-group-text" id="tutorials">hours</span>
                  <div class="invalid-feedback">
                    Value must be a positive integer
                  </div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Practicles</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" :class="!isValidInput('practical') ? 'is-invalid' : ''" placeholder="3"  aria-describedby="practicals" x-model.number="time_allocation.practical" wire:model.defer="time_allocation.practical" min="0">
                  <span class="input-group-text" id="practicals">hours</span>
                  <div class="invalid-feedback">
                    Value must be a positive integer
                  </div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Assignments</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" :class="!isValidInput('assignment') ? 'is-invalid' : ''" placeholder="3"  aria-describedby="assignments" x-model.number="time_allocation.assignment" wire:model.defer="time_allocation.assignment" min="0">
                  <span class="input-group-text" id="assignments">hours</span>
                  <div class="invalid-feedback">
                    Value must be a positive integer
                  </div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Total</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" aria-describedby="total" x-bind:value="calculateTotal()" readonly>
                  <span class="input-group-text" id="total">hours</span>
              </div>
          </div>
      </div>
  </div>                          
</div>