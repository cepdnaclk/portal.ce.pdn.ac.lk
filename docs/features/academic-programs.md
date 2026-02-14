# Academic Programs: Courses and Semesters

## Overview

Academic program content is organized around Semesters and Courses. The API exposes undergraduate curriculum data and supports filtering by curriculum version, semester, and course type.

## Semester model

`App\Domains\AcademicProgram\Semester\Models\Semester`:

- Belongs to an academic program category (`academic_program` + `version`).
- Holds `title`, `description`, and `url`.
- `getLatestCurriculumAttribute()` compares versions of the same title.
- Relationship: `courses()`.

## Course model

`App\Domains\AcademicProgram\Course\Models\Course`:

- Fields include `code`, `name`, `credits`, `type`, `content`, `objectives`, `ilos`, `references`.
- Academic program and curriculum version are stored on the course (`academic_program`, `version`).
- Relationships:
  - `semester()`
  - `modules()` (`CourseModule` entries)
  - `prerequisites()` and `prerequisiteFor()` via `course_prerequisites`.

### Curriculum templates from taxonomy

Courses depend on taxonomy-backed templates cached for 1 hour:

- `Course::getILOTemplate()` reads taxonomy term `course_ilos` and builds the ILO structure.
- `Course::getMarksAllocation()` reads taxonomy term `mark_allocations`.
- `Course::getTimeAllocation()` reads taxonomy term `time_allocations`.

These templates power the JSON structures stored in `ilos`, `marks_allocation`, and `time_allocation`.

## Course modules

`App\Domains\AcademicProgram\Course\Models\CourseModule` contains:

- `topic`, `description`, and `time_allocation` for each module.
- Relationship: `course()`.

## Backend management routes

Routes live in:

- `routes/backend/courses.php`
- `routes/backend/semesters.php`

They are permission-protected and surface Livewire components for listing and editing.

## API endpoints

### Courses

- `GET /api/academic/v1/undergraduate/courses`

Query params:

- `curriculum` (version)
- `semester` (semester_id)
- `type` (course type)

Response fields include modules, prerequisites, and URL helpers for view/edit.

### Semesters

- `GET /api/academic/v1/undergraduate/semesters`

Query params:

- `curriculum` (version)
- `semester` (semester id)

Responses include `courses_count` when loaded via `withCount` in the API controller.
