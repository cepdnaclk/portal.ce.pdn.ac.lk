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
          
        <template x-for="(value, key) in time_allocation" :key="key">
            <div class="row d-flex align-items-center">
                <div class="col-md-3 col-3">
                    <label x-text="key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ')"></label>
                </div>
                <div class="input-group mb-3 col ms-3">
                    <input type="number" class="form-control" aria-describedby="key" :class="!isValidInput(key) ? 'is-invalid' : ''" x-model.number="time_allocation[key]" :wire:model.defer="`time_allocation.${key}`">
                    <span class="input-group-text" id="key">hours</span>
                    <div class="invalid-feedback" >value must be a positive integer</div>
                </div>
            </div>
        </template>

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