   <div x-data="{
       items: @entangle('items') || [],
       dataType: @js($type),
       files: @js($files ?? []),
       pages: @js($pages ?? []),
       userInput: '',
       selectedItem: null,
       editIndex: null,
       isEditing: false,
       hasValidationError: false,
       errorMessage: '',
       modalElement: null,
       init() {
           this.modalElement = document.getElementById('add{{ $type }}ItemModal');
           if (!this.modalElement) {
               return;
           }
           this.modalElement.addEventListener('hide.bs.modal', (event) => {
               if (this.hasValidationError) {
                   event.preventDefault();
                   event.stopImmediatePropagation();
               }
           });
           this.modalElement.addEventListener('hidden.bs.modal', () => {
               this.hasValidationError = false;
               this.errorMessage = '';
           });
       },
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
               this.hasValidationError = false;
               this.errorMessage = '';
               document.getElementById('add{{ $type }}ItemModalLabel').innerHTML = 'Edit ({{ $type }})';
               new bootstrap.Modal(document.getElementById('add{{ $type }}ItemModal')).show();
           }
       },
       addItem() {
           userInput = '';
           document.getElementById('add{{ $type }}ItemModalLabel').innerHTML = 'Add Item ({{ $type }})';
           isEditing = false;
           this.hasValidationError = false;
           this.errorMessage = '';
       },
       validateValue(value) {
           this.errorMessage = '';
           switch (this.dataType) {
               case 'string':
                   if (typeof value !== 'string' || value.trim() === '') {
                       this.errorMessage = 'Please provide a non-empty string.';
                       return null;
                   }
                   return value.trim();
               case 'date':
                   if (!value) {
                       this.errorMessage = 'Please select a date.';
                       return null;
                   }
                   return value;
               case 'url':
                   try {
                       const url = new URL(value);
                       return url.toString();
                   } catch (error) {
                       this.errorMessage = 'Please provide a valid URL.';
                       return null;
                   }
               case 'email':
                   if (typeof value !== 'string' || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                       this.errorMessage = 'Please provide a valid email address.';
                       return null;
                   }
                   return value.trim();
               case 'file':
               case 'page':
                   if (value === '' || value === null) {
                       this.errorMessage = 'Please select an item.';
                       return null;
                   }
                   return Number(value);
               default:
                   return value;
           }
       },
       handleSave() {
           console.log('Saving item:', this.userInput);

           const normalized = this.validateValue(this.userInput);
           if (normalized === null) {
               this.hasValidationError = true;
               if (!this.errorMessage) {
                   this.errorMessage = 'Invalid input. Please correct it before saving.';
               }
               return;
           }
   
           this.hasValidationError = false;
           this.errorMessage = '';
           if (this.isEditing) {
               this.items[this.editIndex] = normalized;
               this.editIndex = null;
               this.isEditing = false;
           } else {
               this.items.push(normalized);
           }
           $wire.emitUp('itemsUpdated', '{{ $type }}', this.items);
           this.userInput = '';
           this.selectedItem = null;
   
           if (!this.hasValidationError && this.modalElement) {
               const modalInstance = bootstrap.Modal.getInstance(this.modalElement) || new bootstrap.Modal(this.modalElement);
               modalInstance.hide();
           }
       },
       getItemLabel(item) {
           if (this.dataType === 'file') {
               const fileId = Number(item);
               const file = this.files.find((entry) => Number(entry.id) === fileId);
               return file ? file.file_name : `Missing file (#${item})`;
           }

           if (this.dataType === 'page') {
               const pageId = Number(item);
               const page = this.pages.find((entry) => Number(entry.id) === pageId);
               return page ? page.slug : `Missing page (#${item})`;
           }

           return typeof item === 'string' ? item : String(item);
       }
   }" x-cloak x-init="$dispatch('items-changed', items)" x-effect="$dispatch('items-changed', items)">
       <style>
           [x-cloak] {
               display: none !important;
           }
       </style>

       <h5>{{ ucfirst($title) }}</h5>

       <div class="d-flex justify-content-between mb-3">
           <button type="button" class="btn btn-primary  btn-w-150" data-bs-toggle="modal"
               data-bs-target="#add{{ $type }}ItemModal" @click="addItem()">
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
                       <template x-if="dataType === 'string'">
                           <textarea rows="6" class="form-control w-100" style="overflow:hidden;" x-model="userInput" autofocus
                               @input="hasValidationError = false; errorMessage = ''"
                               oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"></textarea>
                       </template>
                       <template x-if="dataType === 'date'">
                           <input type="date" class="form-control w-100" x-model="userInput" autofocus
                               @input="hasValidationError = false; errorMessage = ''">
                       </template>
                       <template x-if="dataType === 'url'">
                           <input type="url" class="form-control w-100" x-model="userInput" autofocus
                               @input="hasValidationError = false; errorMessage = ''">
                       </template>
                       <template x-if="dataType === 'email'">
                           <input type="email" class="form-control w-100" x-model="userInput" autofocus
                               @input="hasValidationError = false; errorMessage = ''">
                       </template>
                       <template x-if="dataType === 'file'">
                           <select class="form-select w-100" x-model.number="userInput" autofocus
                               @change="hasValidationError = false; errorMessage = ''">
                               <option value="">Select a file</option>
                               <template x-for="file in files" :key="file.id">
                                   <option :value="file.id" x-text="file.file_name"></option>
                               </template>
                           </select>
                       </template>
                       <template x-if="dataType === 'page'">
                           <select class="form-select w-100" x-model.number="userInput" autofocus
                               @change="hasValidationError = false; errorMessage = ''">
                               <option value="">Select a page</option>
                               <template x-for="page in pages" :key="page.id">
                                   <option :value="page.id" x-text="page.slug"></option>
                               </template>
                           </select>
                       </template>
                       <p class="text-danger mt-2" x-show="errorMessage" x-text="errorMessage"></p>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary" {{-- data-bs-dismiss="modal" --}}
                           x-on:click="handleSave()">Save Changes</button>
                   </div>
               </div>
           </div>
       </div>

       <ul class="list-group" :style="{ 'padding-left': '0', 'margin-left': '0' }">
           <template x-for="(item, index) in items" :key="item + '-' + index">
               <li class="list-group-item position-relative" style="padding: 8px;"
                   :style="{ backgroundColor: selectedItem === index ? '#cfe2ff' : '' }"
                   @click.stop="selectedItem = selectedItem === index ? null : index">
                   <div
                       class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                       <div class="mb-2 mb-md-0 me-md-3">
                           <strong x-text="index + 1 + '.'"></strong>
                           <span x-text="getItemLabel(item)"></span>
                       </div>

                       <div class="d-flex flex-column flex-md-row align-items-md-end ms-auto">
                           <div x-show="selectedItem === index" class="btn-group" role="group"
                               aria-label="Item actions">
                               <button type="button" x-show="items.length > 1" :disabled="index === 0"
                                   class="btn btn-sm btn-secondary me-1 me-md-2 rounded" @click.stop="moveUp()">
                                   <i class="fas fa-chevron-up"></i>
                               </button>
                               <button type="button" x-show="items.length > 1" :disabled="index === items.length - 1"
                                   class="btn btn-sm btn-secondary me-1 me-md-2 rounded" @click.stop="moveDown()">
                                   <i class="fas fa-chevron-down"></i>
                               </button>
                               <button type="button" class="btn btn-sm btn-warning me-1 me-md-2 rounded"
                                   @click.stop="editItem()">
                                   <i class="fas fa-pencil-alt"></i>
                               </button>
                               <button type="button" class="btn btn-sm btn-danger rounded"
                                   @click.stop="deleteItem()">
                                   <i class="fas fa-trash-alt"></i>
                               </button>
                           </div>
                       </div>
                   </div>
               </li>
           </template>
       </ul>

   </div>
