<div x-data="{
    {{-- properties array from parent component can be directly accessed here --}}
    selectedItem: null,
    editIndex: null,
    isEditing: false,
    newProperty: {
        code: '',
        name: '',
        data_type: 'string',
    },
    ClearAll() {
        this.properties = [];
        this.selectedItem = null;
    },
    deleteItem() {
        if (this.selectedItem !== null) {
            this.properties.splice(this.selectedItem, 1);
            this.selectedItem = null;
        }
    },
    moveUp() {
        if (this.selectedItem > 0) {
            let temp = this.properties[this.selectedItem - 1];
            this.properties[this.selectedItem - 1] = this.properties[this.selectedItem];
            this.properties[this.selectedItem] = temp;
            this.selectedItem -= 1;
        }
    },
    moveDown() {
        if (this.selectedItem < this.properties.length - 1) {
            let temp = this.properties[this.selectedItem + 1];
            this.properties[this.selectedItem + 1] = this.properties[this.selectedItem];
            this.properties[this.selectedItem] = temp;
            this.selectedItem += 1;
        }
    },
    editItem() {
        if (this.selectedItem !== null) {
            this.editIndex = this.selectedItem;
            this.newProperty = { ...this.properties[this.selectedItem] };
            this.isEditing = true;
            document.getElementById('addPropertyItemModalLabel').innerHTML = 'Edit Property';
            new bootstrap.Modal(document.getElementById('addPropertyItemModal')).show();
        }
    },
    handleSave() {
        const newPropertyData = {
            code: this.newProperty.code.trim(),
            name: this.newProperty.name.trim(),
            data_type: this.newProperty.data_type
        };

        if (this.isEditing && this.editIndex !== null) {
            if (this.editIndex >= 0 && this.editIndex < this.properties.length) {
                this.properties[this.editIndex] = newPropertyData;
                this.selectedItem = this.editIndex; // Select the edited item
            } else {
                console.error('Invalid editIndex:', this.editIndex);
            }
            this.editIndex = null;
            this.isEditing = false;
        } else {
            this.properties.push(newPropertyData);
            this.selectedItem = this.properties.length - 1; // Select the newly added item
        }
        bootstrap.Modal.getInstance(document.getElementById('addPropertyItemModal')).hide();
        {{-- nextTick ensures that the modal is hidden before resetting the form --}}
        this.$nextTick(() => {
            this.resetForm();
        });
    },
    resetForm() {
        this.newProperty = {
            code: '',
            name: '',
            data_type: 'string'
        };
    },

}" x-cloak>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="d-flex justify-content-between mb-3">
        <button type="button" class="btn btn-primary btn-w-150" data-bs-toggle="modal" data-bs-target="#addPropertyItemModal"
            x-on:click="resetForm(); document.getElementById('addPropertyItemModalLabel').innerHTML = 'Add Property'; isEditing=false">
            <i class="fas fa-plus me-2"></i>Add
        </button>
        <button type="button" class="btn btn-dark btn-w-150" x-show="properties.length" x-transition x-on:click="ClearAll()">
            <i class="fas fa-times me-2"></i>Clear All
        </button>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="addPropertyItemModal" tabindex="-1" aria-labelledby="addPropertyItemModalLabel"
        aria-hidden="true" x-init="$el.addEventListener('hidden.bs.modal', () => resetForm())">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addPropertyItemModalLabel">Add Property</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form >
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="propertyCode" class="form-label">Code*</label>
                                    <input type="text" class="form-control" id="propertyCode" 
                                        x-model="newProperty.code" />
                                </div>
                                <div class="mb-3">
                                    <label for="propertyName" class="form-label">Name*</label>
                                    <input type="text" class="form-control" id="propertyName" 
                                        x-model="newProperty.name"/>
                                </div>
                                <div class="mb-3">
                                    <label for="propertyDatatype" class="form-label">Datatype</label>
                                    <select class="form-select" aria-label="Datatype" x-model="newProperty.data_type">
                                        <option selected value="string">String</option>
                                        <option value="integer">Integer Number</option>
                                        <option value="float">Floating Point Number</option>
                                        <option value="date">Date</option>
                                        <option value="datetime">Date Time</option>
                                        <option value="boolean">Boolean</option>
                                        <option value="url">URL</option>
                                        <option value="image">Image</option>
                                      </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-w-150" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-w-150"
                        x-on:click="handleSave()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <ul class="list-group" :style="{ 'padding-left': '0', 'margin-left': '0' }">
        <template x-for="(item, index) in properties" :key="item + '-' + index">
            <li class="list-group-item position-relative" style="padding: 8px;"
                :style="{ backgroundColor: selectedItem === index ? '#cfe2ff' : '' }"
                x-on:click="selectedItem = selectedItem === index ? null : index">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-2 mb-md-0 me-md-3">
                        <strong x-text="index + 1 + '.'"></strong>
                        <span x-text="item.name"></span>
                        <span>(</span>
                        <span x-text="item.code"></span>
                        <span>,</span>
                        <span x-text="item.data_type"></span>
                        <span>)</span>
                    </div>

                    <div class="d-flex flex-column flex-md-row align-items-md-end ms-auto">
                        <div x-show="selectedItem === index" class="btn-group" role="group" aria-label="Item actions">
                            <button type="button" class="btn btn-sm btn-secondary me-1 me-md-2 rounded"
                                @click.stop="moveUp()">
                                <i class="fas fa-chevron-up"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary me-1 me-md-2 rounded"
                                @click.stop="moveDown()">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning me-1 me-md-2 rounded"
                                @click.stop="editItem()">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger rounded" @click.stop="deleteItem()">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </li>
        </template>
    </ul>
</div>
