<div x-data="{
    items: @entangle('items') || [],
    userInput: '',
    selectedItem: null,
    editIndex: null,
    isEditing: false,
    ClearAll() {
        this.items = [];
        this.selectedItem = null;
        $wire.emitUp('itemsUpdated', '{{ $type }}', this.items);
    },
    deleteItem() {
        if (this.selectedItem !== null) {
            this.items.splice(this.selectedItem, 1);
            $wire.emitUp('itemsUpdated', '{{ $type }}', this.items);
            this.selectedItem = null;
        }
    },
    moveUp() {
        if (this.selectedItem > 0) {
            let temp = this.items[this.selectedItem - 1];
            this.items[this.selectedItem - 1] = this.items[this.selectedItem];
            this.items[this.selectedItem] = temp;
            this.selectedItem -= 1;
            $wire.emitUp('itemsUpdated', '{{ $type }}', this.items);
        }
    },
    moveDown() {
        if (this.selectedItem < this.items.length - 1) {
            let temp = this.items[this.selectedItem + 1];
            this.items[this.selectedItem + 1] = this.items[this.selectedItem];
            this.items[this.selectedItem] = temp;
            this.selectedItem += 1;
            $wire.emitUp('itemsUpdated', '{{ $type }}', this.items);
        }
    },
    editItem() {
        if (this.selectedItem !== null) {
            this.editIndex = this.selectedItem;
            this.userInput = this.items[this.selectedItem];
            this.isEditing = true;
            document.getElementById('add{{ $type }}ItemModalLabel').innerHTML = 'Edit {{ $type }}';
            new bootstrap.Modal(document.getElementById('add{{ $type }}ItemModal')).show();
        }
    },
    handleSave() {
        if (this.userInput.trim().length > 0) {
            if (this.isEditing) {
                this.items[this.editIndex] = this.userInput;
                this.editIndex = null;
                this.isEditing = false;
            } else {
                this.items.push(this.userInput);
            }
            $wire.emitUp('itemsUpdated', '{{ $type }}', this.items);
            this.userInput = '';
            this.selectedItem = null;
        }
    }
}" x-cloak>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <h5>{{ ucfirst($type) }}</h5>
    <div class="d-flex justify-content-between mb-3">
        <button type="button" class="btn btn-primary  btn-w-150" data-bs-toggle="modal"
            data-bs-target="#add{{ $type }}ItemModal"
            @click="userInput = '';document.getElementById('add{{ $type }}ItemModalLabel').innerHTML = 'Add {{ $type }}';isEditing=false">
            <i class="fas fa-plus me-2"></i>Add
        </button>
        <button class="btn btn-dark btn-w-150" x-show="items.length" x-transition @click="ClearAll()">
            <i class="fas fa-times me-2"></i>Clear All
        </button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="add{{ $type }}ItemModal" tabindex="-1"
        aria-labelledby="add{{ $type }}ItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add{{ $type }}ItemModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea class="form-control w-100" style="overflow:hidden;" x-model="userInput" autofocus
                        oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                        x-on:click="handleSave()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <ul class="list-group" :style="{ 'padding-left': '0', 'margin-left': '0' }">
        <template x-for="(item, index) in items" :key="item + '-' + index">
            <li class="list-group-item position-relative" style="padding: 8px;"
                :style="{ backgroundColor: selectedItem === index ? '#cfe2ff' : '' }"
                @click="selectedItem = selectedItem === index ? null : index">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-2 mb-md-0 me-md-3">
                        <strong x-text="index + 1 + '.'"></strong>
                        <span x-text="item"></span>
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
