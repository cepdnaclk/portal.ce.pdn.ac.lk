@props(['selected', 'optionsMap'])
<div class="form-group row" x-data="{
    selectedTypes: ['{{ implode("','", $selected ?? []) }}'],
    typeMap: {{ json_encode($optionsMap) }}
}"
>
{!! Form::label('event_type', 'Event Type*', ['class' => 'col-md-2 col-form-label']) !!}
<div class="col-md-5">
    <div class="dropdown">
        <button class="btn dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #d3d3d3; color: #000;">
            Select Event Type
        </button>
        <ul class="dropdown-menu w-100 p-3" aria-labelledby="dropdownMenuButton" style="max-height: 300px; overflow-y: auto;">
            <template x-for="(value, key) in typeMap" :key="key">
                <li class="dropdown-item" 
                    style="outline: none; box-shadow: none; background-color: transparent; color: black;" 
                    onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                    onmouseout="this.style.backgroundColor='transparent';">
                    <label class="form-check-label d-block w-100" :for="'event_type_' + key">
                        <input class="form-check-input me-2" type="checkbox" name="event_type[]" :value="key" :id="'event_type_' + key"
                            x-model="selectedTypes" :checked="selectedTypes.includes(value)">
                        <span x-text="value"></span>
                    </label>
                </li>
            </template>
        </ul>
    </div>
    @error('event_type')
        <strong class="text-danger">{{ $message }}</strong>
    @enderror
</div>

<!-- Selected tags appear here -->
<div class="col-md-5">
    <div id="selected-tags" class="mt-2">
        <template x-for="type in selectedTypes" :key="type">
            <span x-show="typeMap[type]" class="badge bg-primary me-2">
                <span x-text="typeMap[type]"></span>
            </span>
        </template>
    </div>
</div>
</div>