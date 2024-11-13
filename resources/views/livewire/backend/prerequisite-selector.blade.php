<div>
    <div class="container">
        <div class="row">
            <!-- Available Courses -->
            <div class="col-md-6">
                <div class="p-2 bg-light border rounded">
                    <h5>Available Courses</h5>
                    <input wire:model.debounce.300ms="searchTerm" type="text" class="form-control mb-3"
                        placeholder="Search course by name or code">

                    <ul class="list-group overflow-auto" style="max-height: 400px;">
                        @forelse ($filteredAvailableCourses as $course)
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                wire:key="available-{{ $course->id }}"
                                wire:click="courseSelected({{ $course->id }})">
                                <div class="font-weight-bold">
                                    {{ $course->code }}
                                    <small class="ms-2">
                                        {{ $course->name }} |
                                        {{ $course->semester->title }} | {{ $course->type }}
                                    </small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </li>
                        @empty
                            <li class="list-group-item h6 text-muted font-italic">No available courses</li>
                        @endforelse
                    </ul>

                    <div class="mt-3">
                        {{ $filteredAvailableCourses->links() }}
                    </div>
                </div>
            </div>

            <!-- Prerequisites -->
            <div class="col-md-6">
                <div class="p-2 bg-light border rounded">
                    <h5>Prerequisites</h5>
                    <ul class="list-group overflow-auto" style="max-height: 400px;">
                        @forelse ($selectedCourses as $course)
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                wire:key="selected-{{ $course['id'] }}" wire:click="courseRemoved({{ $course['id'] }})">
                                <div class="font-weight-bold">
                                    {{ $course['code'] }}
                                    <small class="ms-2">
                                        {{ $course['name'] }} |
                                        Semester: {{ $course['semester_id'] }} | {{ $course['type'] }}
                                    </small>
                                </div>
                                <i class="fas fa-chevron-left"></i>
                            </li>
                        @empty
                            <li class="list-group-item h6 text-muted font-italic">No selected prerequisites</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
