@props(['selected', 'optionsMap'])
<div class="form-group row" x-data="{
    selectedTypes: ['{{ implode("','", $selected ?? []) }}'],
    typeMap: {{ json_encode($optionsMap) }}
}">
    {!! Form::label('event_type', 'Event Type*', ['class' => 'col-md-2 col-form-label']) !!}
    <div class="col-md-4">
        <div class="dropdown border border-1 rounded">
            <button class="btn w-100 form-select" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                aria-expanded="false">
                Select Event Type
            </button>
            <ul class="dropdown-menu w-100 p-1 rounded-0" aria-labelledby="dropdownMenuButton"
                style="max-height: 300px; overflow-y: auto;">
                <template x-for="(value, key) in typeMap" :key="key">
                    <li class="dropdown-item m-0" onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                        onmouseout="this.style.backgroundColor='transparent';">
                        <div class="form-check">
                            <input class="form-check-input me-2" type="checkbox" name="event_type[]"
                                :value="key" :id="'event_type_' + key" x-model="selectedTypes"
                                :checked="selectedTypes.includes(value)">
                            <label class="form-check-label d-block" :for="'event_type_' + key">
                                <span x-text="value"></span>
                            </label>
                        </div>
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
