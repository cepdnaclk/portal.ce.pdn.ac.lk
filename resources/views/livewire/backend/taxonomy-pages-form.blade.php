<div>
    <form wire:submit.prevent="save">
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" class="form-control" wire:model.defer="slug">
            @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label for="taxonomy" class="form-label">Taxonomy</label>
            <select wire:model="taxonomy_id" id="taxonomy" class="form-control">
                <option value="">— none —</option>
                @foreach($taxonomies as $taxonomy)
                    <option value="{{ $taxonomy->id }}">{{ $taxonomy->name }}</option>
                @endforeach
            </select>
            @error('taxonomy_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <livewire:backend.richtext-editor-component :name="page-html" :value="html" />
            @error('html') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
