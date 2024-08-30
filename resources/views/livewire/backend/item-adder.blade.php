<div>
    <div x-data="{ 
        items: @entangle('items'),
        userInput: '', 
        selectedItem: null, 
        editIndex: null,
        isEditing: false
    }">
        {{-- <form wire:submit.prevent>
            <div class="input-group mb-3 {{$size}}" style="margin-left: 0;">
                <input x-model="userInput" placeholder="Enter item" type="text" class="form-control item-box">
                <div class="input-group-append">
                    <button class="btn btn-primary add-btn" x-text="isEditing ? 'Update' : 'Add'" x-on:click="
                        if(userInput.trim().length > 0) {
                            if (isEditing) {
                                items[editIndex] = userInput;
                                editIndex = null;
                                isEditing = false;
                            } else {
                                items.push(userInput);
                            }
                            $wire.emitUp('itemsUpdated', '{{ $type }}', items);
                            userInput = '';
                            selectedItem = null;
                        }">
                    </button>
                </div>
            </div>
        </form> --}}
        <form wire:submit.prevent>
            <div class="input-group mb-3 {{$size}}" style="margin-left: 0;">
                <div class="form-floating" style="width: 94%;  display: flex; flex-direction: row;">
                    
                    {{-- <input x-model="userInput" placeholder="Enter item" type="text" class="form-control item-box" > --}}
                    @if ($type === 'references')
                    <input x-model="userInput" placeholder="Enter item" type="text" class="form-control item-box">
                    @else
                        <textarea x-model="userInput" placeholder="Enter item" class="form-control item-box" style="height: auto; resize: vertical; overflow: hidden;"></textarea>
                    @endif

                    <label for="itemInput"> {{ ucfirst($type) }} </label>

                </div>
                <div class="input-group-append" style="margin-left: 10px;">
                    <button class="btn btn-primary add-btn" x-text="isEditing ? 'Update' : 'Add'" x-on:click="
                        if(userInput.trim().length > 0) {
                            if (isEditing) {
                                items[editIndex] = userInput;
                                editIndex = null;
                                isEditing = false;
                            } else {
                                items.push(userInput);
                            }
                            $wire.emitUp('itemsUpdated', '{{ $type }}', items);
                            userInput = '';
                            selectedItem = null;
                        }" style="height: 58px;">
                    </button>
                </div>
            </div>
        </form>
        

        <ul class="list-group custom-list  ml-3">
            <template x-for="(item, index) in items" :key="index">
                <li class="list-group-item custom-list-item d-flex justify-content-between align-items-center"
                    :class="{'active': selectedItem === index}"
                    @click="selectedItem = selectedItem === index ? null : index">
                    <span>
                        <strong x-text="index + 1 + '. '"></strong>
                        <span x-text="item"></span>
                    </span>
                    <span class="badge badge-primary badge-pill" x-text="selectedItem === index ? 'Selected' : ''"></span>
                </li>
            </template>
        </ul>

        <div class="mt-3 ml-3">
            <button @click="if(selectedItem !== null) { 
                editIndex = selectedItem; 
                userInput = items[selectedItem]; 
                isEditing = true;
            }" class="btn btn-outline-warning btn-sm" :disabled="selectedItem === null">✏️ Edit</button>
            <button @click="if(selectedItem !== null) { 
                items.splice(selectedItem, 1); 
                $wire.emitUp('itemsUpdated', '{{ $type }}', items);
                selectedItem = null; 
            }" class="btn btn-danger btn-sm ml-1" :disabled="selectedItem === null">🗑️ Delete</button>
            <button @click="if(selectedItem > 0) { 
                let temp = items[selectedItem-1]; 
                items[selectedItem-1] = items[selectedItem]; 
                items[selectedItem] = temp; 
                selectedItem -= 1; 
                $wire.emitUp('itemsUpdated', '{{ $type }}', items);
            }" class="btn btn-secondary btn-sm ml-1" :disabled="selectedItem === null || selectedItem === 0">⬆️ Up</button>
            <button @click="if(selectedItem < items.length - 1) { 
                let temp = items[selectedItem+1]; 
                items[selectedItem+1] = items[selectedItem]; 
                items[selectedItem] = temp; 
                selectedItem += 1; 
                $wire.emitUp('itemsUpdated', '{{ $type }}', items);
            }" class="btn btn-secondary btn-sm ml-1" :disabled="selectedItem === null || selectedItem === items.length - 1">⬇️ Down</button>

            <button @click="items = []; selectedItem = null; $wire.emitUp('itemsUpdated', '{{ $type }}', items);" x-show="items.length" class="clear-btn btn btn-warning ml-2">Clear All</button>

        </div>

        
        {{-- <button @click="items = []; selectedItem = null; $wire.emitUp('itemsUpdated', '{{ $type }}', items);" x-show="items.length" class="clear-btn btn btn-warning mt-2">Clear All</button> --}}
        
    </div>
</div>

<style>
    .custom-list {
        margin-bottom: 10px;
    }
    .custom-list-item {
        padding: 5px 10px;
        font-size: 14px;
    }
    .list-group-item.active {
        background-color: #007bff;
        color: white;
    }
    .form-control {
        
        padding: 5px 10px;
    }
    
    
</style>
