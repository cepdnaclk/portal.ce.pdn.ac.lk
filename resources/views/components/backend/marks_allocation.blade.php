<div class="col-md-6" x-data="{ 
            marks_allocation: $wire.entangle('marks_allocation'),
            totalIsValid : true,
            isEmpty: true, // Track whether all fields are empty
            isValidInput(key) {
                const value = Number(this.marks_allocation[key]);
                if (value === null || value === undefined || value === '') {
                    return { valid: true }; // Empty values are valid
                }
                if (value < 0) {
                    return { valid: false, message: 'Value can\'t be negative' };
                }
                if (value > 100) {
                    return { valid: false, message: 'Value can\'t exceed 100' };
                }
                if (!Number.isInteger(value)) {
                console.log(value);
                    return { valid: false, message: 'Value must be a positive integer' };
                }
                return { valid: true };
              },
            calculateTotal() {
                let total = 0;
                let hasValue = false;
                
                Object.keys(this.marks_allocation).forEach(key => {
                    const value = Number(this.marks_allocation[key]);
                    if (value) {
                        hasValue = true; // At least one field has a value
                    }
                    if (this.isValidInput(key).valid) {
                        total += (value || 0);
                    }
                });

                this.isEmpty = !hasValue; // Update isEmpty based on presence of values
                this.totalIsValid = total == 100;
                return total;
            }
          }">
  <div class="py-2 px-3 my-2" style="border: 1px solid rgb(207, 207, 207); border-radius:5px">
      <div class="div pt-3">
          <label for="drop1">Marks Allocation</label>
      </div>
      <hr>
      <div class="row pb-2">
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Practicals</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" aria-describedby="practicals" :class="!isValidInput('practicals').valid ? 'is-invalid' : ''" placeholder="20" x-model.number="marks_allocation.practicals" wire:model.defer="marks_allocation.practicals" min="0" max="100">
                  <span class="input-group-text" id="practicles">%</span>
                  <div class="invalid-feedback" x-text="isValidInput('practicals').message"></div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Project</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" aria-describedby="project" :class="!isValidInput('project').valid ? 'is-invalid' : ''" placeholder="20" x-model.number="marks_allocation.project" wire:model.defer="marks_allocation.project" min="0" max="100">
                  <span class="input-group-text" id="project">%</span>
                  <div class="invalid-feedback" x-text="isValidInput('project').message"></div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label style="white-space: nowrap;">Mid-Exam</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" aria-describedby="mid_exam" :class="!isValidInput('mid_exam').valid ? 'is-invalid' : ''" placeholder="20" x-model.number="marks_allocation.mid_exam" wire:model.defer="marks_allocation.mid_exam" min="0" max="100">
                  <span class="input-group-text" id="mid_exam">%</span>
                  <div class="invalid-feedback" x-text="isValidInput('mid_exam').message"></div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label style="white-space: nowrap;">End-Exam</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" aria-describedby="end_exam" :class="!isValidInput('end_exam').valid ? 'is-invalid' : ''" placeholder="20" x-model.number="marks_allocation.end_exam" wire:model.defer="marks_allocation.end_exam" min="0" max="100">
                  <span class="input-group-text" id="end_exam">%</span>
                  <div class="invalid-feedback" x-text="isValidInput('end_exam').message"></div>
              </div>
          </div>
          <div class="row d-flex align-items-center">
              <div class="col-md-3 col-3">
                  <label>Total</label>
              </div>
              <div class="input-group mb-3 col ms-3">
                  <input type="number" class="form-control" aria-describedby="total" :class="(!totalIsValid && !isEmpty) ? 'is-invalid' : ''" x-bind:value="calculateTotal()" readonly>
                  <span class="input-group-text" id="total">%</span>
                  <div class="invalid-feedback" x-show="!totalIsValid && !isEmpty" x-text="'Total should be 100%'"></div>
              </div>
          </div>
      </div>
  </div>                          
</div>