<div x-data="{
    modules: @entangle('modules').defer,
    selectedItem: null,
    editIndex: null,
    isEditing: false,
    moduleNameError: false,
    newModule: {
        name: '',
        description: '',
        time_allocation: JSON.parse(JSON.stringify($wire.module_time_allocation)),
    },
    ClearAll() {
        this.modules = [];
        this.selectedItem = null;
    },
    deleteItem() {
        if (this.selectedItem !== null) {
            this.modules.splice(this.selectedItem, 1);
            this.selectedItem = null;
        }
    },
    moveUp() {
        if (this.selectedItem > 0) {
            let temp = this.modules[this.selectedItem - 1];
            this.modules[this.selectedItem - 1] = this.modules[this.selectedItem];
            this.modules[this.selectedItem] = temp;
            this.selectedItem -= 1;
        }
    },
    moveDown() {
        if (this.selectedItem < this.modules.length - 1) {
            let temp = this.modules[this.selectedItem + 1];
            this.modules[this.selectedItem + 1] = this.modules[this.selectedItem];
            this.modules[this.selectedItem] = temp;
            this.selectedItem += 1;
        }
    },
    editItem() {
        if (this.selectedItem !== null) {
            this.editIndex = this.selectedItem;
            this.newModule = { ...this.modules[this.selectedItem] };
            this.isEditing = true;
            this.moduleNameError = false;
            document.getElementById('addModuleItemModalLabel').innerHTML = 'Edit Module';
            new bootstrap.Modal(document.getElementById('addModuleItemModal')).show();
        }
    },
    handleSave() {
        if (this.newModule.name && this.newModule.name.trim().length > 0) {
            if (!Array.isArray(this.modules)) {
                this.modules = [];
            }

            const newModuleData = {
                name: this.newModule.name.trim(),
                description: this.newModule.description.trim(),
                time_allocation: { ...this.newModule.time_allocation }
            };

            if (this.isEditing && this.editIndex !== null) {
                if (this.editIndex >= 0 && this.editIndex < this.modules.length) {
                    this.modules[this.editIndex] = newModuleData;
                    this.selectedItem = this.editIndex; // Select the edited item
                } else {
                    console.error('Invalid editIndex:', this.editIndex);
                }
                this.editIndex = null;
                this.isEditing = false;
            } else {
                this.modules.push(newModuleData);
                this.selectedItem = this.modules.length - 1; // Select the newly added item
            }

            bootstrap.Modal.getInstance(document.getElementById('addModuleItemModal')).hide();
            {{-- nextTick ensures that the modal is hidden before resetting the form --}}
            this.$nextTick(() => {
                this.resetForm();
            });
        } else {
            this.moduleNameError = true;
        }
    },
    resetForm() {
        this.newModule = {
            name: '',
            description: '',
            time_allocation: JSON.parse(JSON.stringify(this.$wire.module_time_allocation))
        };
        this.moduleNameError = false;
    },

}" x-cloak>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <h5>Modules</h5>
    <div class="d-flex justify-content-between mb-3">
        <button type="button" class="btn btn-primary btn-w-150" data-bs-toggle="modal" data-bs-target="#addModuleItemModal"
            @click="resetForm(); document.getElementById('addModuleItemModalLabel').innerHTML = 'Add Module'; isEditing=false">
            <i class="fas fa-plus me-2"></i>Add
        </button>
        <button class="btn btn-dark btn-w-150" x-show="modules.length" x-transition @click="ClearAll()">
            <i class="fas fa-times me-2"></i>Clear All
        </button>
    </div>

    {{-- Accordion --}}
    <div class="accordion" id="accordionExample">
        <template x-for="(module, index) in modules.filter(m => m && m.name)" :key="module.name + index">
            <div class="accordion-item">
                <h2 class="accordion-header" :id="'heading' + index">
                    <div class="accordion-button d-flex justify-content-between align-items-center p-0 pe-2"
                        type="button" :data-bs-target="'#collapse' + index" :aria-expanded="selectedItem === index"
                        :class="{ 'collapsed': selectedItem !== index }" :aria-controls="'collapse' + index"
                        @click="selectedItem = selectedItem === index ? null : index">
                        <strong style="padding-left: 8px;" x-text="index + 1 + '. '"></strong>
                        <span x-text="module.name" class="flex-grow-1 text-start p-3 ps-1"
                            style="font-weight: normal;"></span>
                        <div x-show="selectedItem === index" class="btn-group" role="group" aria-label="Item actions"
                            @click.stop>
                            <button type="button" x-show="modules.length > 1" :disabled="index === 0" class="btn btn-sm btn-secondary rounded me-2" @click.stop="moveUp()">
                                <i class="fas fa-chevron-up"></i>
                            </button>
                            <button type="button" x-show="modules.length > 1" :disabled="index === modules.length - 1" class="btn btn-sm btn-secondary rounded me-2" @click.stop="moveDown()">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning rounded me-2" @click.stop="editItem()">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger rounded" @click.stop="deleteItem()">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </h2>
                <div :id="'collapse' + index" class="accordion-collapse collapse"
                    :class="{ 'show': selectedItem === index }" :aria-labelledby="'heading' + index"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <b>Module Description :</b>
                        <p x-text="module.description || 'No description available'"></p>
                        <span
                            x-show="module.time_allocation && Object.values(module.time_allocation).some(value => value != null && value !== 0)"
                            x-text="Object.entries(module.time_allocation)
                                     .filter(([key, value]) => value !== null && value !== 0 && value !=='')
                                     .map(([key, value]) => key.charAt(0).toUpperCase() + key.slice(1) + ': ' + value + ' h')
                                     .join(', ')">
                        </span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addModuleItemModal" tabindex="-1" aria-labelledby="addModuleItemModalLabel"
        aria-hidden="true" x-init="$el.addEventListener('hidden.bs.modal', () => resetForm())">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addModuleItemModalLabel">Add Module</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 mb-3 h-full">
                                    <div class="border rounded p-3">
                                        <h4 class="mb-3">Module Description</h4>
                                        <div class="mb-3">
                                            <label for="moduleName" class="form-label">Name*</label>
                                            <input type="text" class="form-control" id="moduleName" autofocus
                                                x-model="newModule.name"
                                                :class="{
                                                    'is-invalid': moduleNameError,
                                                    'is-valid': newModule.name.trim()
                                                        .length > 0
                                                }"
                                                @input="moduleNameError = false" required>
                                            <div id="moduleNameError" class="invalid-feedback" x-show="moduleNameError">
                                                Please enter a module name.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="moduleDescription" class="form-label">Description</label>
                                            <textarea class="form-control w-100" rows="10" style="overflow:hidden;" id="moduleDescription"
                                                x-model="newModule.description" oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h4 class="mb-3">Time Allocation</h4>
                                        <template x-for="(value, key) in newModule.time_allocation"
                                            :key="key + '-' + value + '-' + newModule.name">
                                            <div class="mb-2">
                                                <label :for="key" class="form-label"
                                                    x-text="key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ')"></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" :id="key"
                                                        x-model="newModule.time_allocation[key]">
                                                    <span class="input-group-text">hours</span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-w-150" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-w-150"
                        x-on:click="!moduleNameError && handleSave()">Save
                        Changes</button>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .accordion-button::after {
        display: none !important;
    }
</style>
