<div class="container mx-auto mt-6 px-3 py-2">
    <div 
        x-data="{
            modules: @entangle('modules'),
            nextId: 1,
            addModule() {
                const module = {
                    id: this.nextId++,
                    name: '',
                    description: '',
                    time_allocation: {
                        lectures: 0,
                        tutorials: 0,
                        practicals: 0,
                        assignments: 0
                    }
                };

                // Push to the local array
                this.modules.push(module);
                // Manually sync with Livewire
                $wire.set('modules', this.modules);
            },
            removeModule(index) {
                // Remove from the local array
                this.modules.splice(index, 1);
                // Manually sync with Livewire
                $wire.set('modules', this.modules);
            }
        }"
    >
        <template x-for="(module, index) in modules" :key="module.id">
            <div x-transition x-cloak class="px-3 py-3 my-6 rounded mb-4" style="border: 1px solid rgb(209, 209, 209)">
                <div class="mb-3">
                    <label>Name</label>
                    <input 
                        type="text"  
                        class="form-control border p-2 mb-2 rounded-md"
                        x-model="modules[index].name"
                        x-on:input="$wire.set('modules', modules)">
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <div class="form-floating mb-4">
                        <textarea 
                            class="form-control auto-resize-textarea"
                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                            x-model="modules[index].description"
                            x-on:input="$wire.set('modules', modules)"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col d-flex align-items-center col-md-3 col-6">
                        <label>Lectures</label>
                        <input 
                            class="form-control"
                            type="number"
                            x-model.number="modules[index].time_allocation.lectures"
                            x-on:input="$wire.set('modules', modules)">
                    </div>
                    <div class="col d-flex align-items-center col-md-3 col-6">
                        <label>Tutorials</label>
                        <input 
                            class="form-control"
                            type="number"
                            x-model.number="modules[index].time_allocation.tutorials"
                            x-on:input="$wire.set('modules', modules)">
                    </div>
                    <div class="col d-flex align-items-center col-md-3 col-6">
                        <label>Practicals</label>
                        <input 
                            class="form-control"
                            type="number"
                            x-model.number="modules[index].time_allocation.practicals"
                            x-on:input="$wire.set('modules', modules)">
                    </div>
                    <div class="col d-flex align-items-center col-md-3 col-6">
                        <label>Assignments</label>
                        <input 
                            class="form-control"
                            type="number"
                            x-model.number="modules[index].time_allocation.assignments"
                            x-on:input="$wire.set('modules', modules)">
                    </div>
                </div>
                <button
                    x-on:click="removeModule(index)"
                    class="btn btn-danger px-4 py-2 rounded-md mt-2">
                    Delete
                </button>
            </div>
        </template>
        <button  
            x-on:click="addModule()"
            class="btn btn-primary px-4 py-2 mb-3 rounded-md">
            Add Module
        </button>
    </div>
</div>
