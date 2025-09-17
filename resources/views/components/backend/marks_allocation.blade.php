<div class="col-12 col-sm-6" x-data="{
    marks_allocation: $wire.entangle('marks_allocation'),
    totalIsValid: true,
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

            <x-backend.taxonomy_tooltip
                edit-url="{{ route('dashboard.taxonomy.alias', ['code' => 'mark_allocations']) }}" placement="auto"
                class="float-end">
            </x-backend.taxonomy_tooltip>
        </div>
        <hr>
        <div class="row pb-2">

            <template x-for="(value, key) in marks_allocation" :key="key">
                <div class="row d-flex align-items-center">
                    <div class="col-md-4 col-12">
                        <label x-text="key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ')"></label>
                    </div>
                    <div class="input-group mb-3 col ms-3">
                        <input type="number" value="0" class="form-control" aria-describedby="key"
                            :class="!isValidInput(key).valid ? 'is-invalid' : ''" x-model.number="marks_allocation[key]"
                            :wire:model.defer="`marks_allocation.${key}`" min="0" max="100">
                        <span class="input-group-text" id="key">%</span>
                        <div class="invalid-feedback" x-text="isValidInput(key).message"></div>
                    </div>
                </div>
            </template>

            <div class="row d-flex align-items-center">
                <div class="col-md-4 col-12">
                    <label>Total</label>
                </div>
                <div class="input-group mb-3 col ms-3">
                    <input type="number" class="form-control" aria-describedby="total"
                        :class="(!totalIsValid && !isEmpty) ? 'is-invalid' : ''" x-bind:value="calculateTotal()"
                        readonly>
                    <span class="input-group-text" id="total">%</span>
                    <div class="invalid-feedback" x-show="!totalIsValid && !isEmpty" x-text="'Total should be 100%'">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
