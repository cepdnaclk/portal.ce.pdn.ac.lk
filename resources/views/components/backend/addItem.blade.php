<div class="container">
    <div class="row">
        <div class="col-5">
            <div x-data="{ 
                items: JSON.parse(localStorage.getItem('items') || '[]'), 
                userInput: '', 
                selectedItem: null, 
                editIndex: null 
            }" 
            x-init="$watch('items', value => localStorage.setItem('items', JSON.stringify(value)))">
                <form x-on:submit.prevent>
                    <div class="input-group mb-3">
                        <input x-model="userInput" placeholder="Enter item" type="text" class="form-control item-box">
                        <div class="input-group-append">
                            <button class="btn btn-primary add-btn" x-on:click="if(userInput.trim().length > 0) {
                                    if (editIndex !== null) {
                                        items[editIndex] = userInput;
                                        editIndex = null;
                                    } else {
                                        items.push(userInput);
                                    }
                                    userInput = '';
                                    selectedItem = null;
                                }">Add</button>
                        </div>
                    </div>
                </form>

                <ul class="list-group">
                    <template x-for="(item, index) in items" :key="index">
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            :class="{'active': selectedItem === index}"
                            @click="selectedItem = index">
                            <span x-text="item"></span>
                            <span class="badge badge-primary badge-pill" x-text="selectedItem === index ? 'Selected' : ''"></span>
                        </li>
                    </template>
                </ul>

                <div class="mt-3">
                    <button @click="if(selectedItem !== null) { editIndex = selectedItem; userInput = items[selectedItem]; }" class="btn btn-outline-warning btn-sm" :disabled="selectedItem === null">✏️ Edit</button>
                    <button @click="if(selectedItem !== null) { 
                        items.splice(selectedItem, 1); 
                        selectedItem = null; 
                        localStorage.setItem('items', JSON.stringify(items)); 
                    }" class="btn btn-danger btn-sm ml-1" :disabled="selectedItem === null">🗑️ Delete</button>
                    <button @click="if(selectedItem > 0) { 
                        let temp = items[selectedItem-1]; 
                        items[selectedItem-1] = items[selectedItem]; 
                        items[selectedItem] = temp; 
                        selectedItem -= 1; 
                        localStorage.setItem('items', JSON.stringify(items)); 
                    }" class="btn btn-secondary btn-sm ml-1" :disabled="selectedItem === null || selectedItem === 0">⬆️ Up</button>
                    <button @click="if(selectedItem < items.length - 1) { 
                        let temp = items[selectedItem+1]; 
                        items[selectedItem+1] = items[selectedItem]; 
                        items[selectedItem] = temp; 
                        selectedItem += 1; 
                        localStorage.setItem('items', JSON.stringify(items)); 
                    }" class="btn btn-secondary btn-sm ml-1" :disabled="selectedItem === null || selectedItem === items.length - 1">⬇️ Down</button>
                </div>

                <button @click="items = []; localStorage.removeItem('items'); selectedItem = null;" x-show="items.length" class="clear-btn btn btn-warning mt-2">Clear All</button>
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item.active {
        background-color: #007bff;
        color: white;
    }
</style>
