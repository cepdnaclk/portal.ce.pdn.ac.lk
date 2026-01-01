# portal.ce.pdn.ac.lk — Software Requirements Specification: Phase 2

## 1. Purpose and Scope

- Purpose: Capture requirements for the Academic Program and Course Management capabilities introduced in phase 2.
- Scope: Dashboard management of semesters and courses (including prerequisites), reuse of phase 1 authentication/localization, and preservation of phase 1 CMS modules.
- Out of scope: Taxonomy-first authoring (added in phase 3) beyond the taxonomy lookups already consumed by courses.

## 2. References

- Routes: `routes/backend/{academic_program.php,semesters.php,courses.php}`.
- Data models: `database/migrations/2024_08_23_165504_create_semesters_table.php`, `2024_09_03_102546_create_courses_table.php`, `2024_10_07_120321_create_course_prerequisites_table.php`, `2024_10_11_124838_alter_column_version.php`, `2025_07_19_134835_update_enums_to_varchar.php`.
- Controllers/flows: `App\Http\Controllers\Backend\{SemesterController,CourseController}` and Livewire `App\Http\Livewire\Backend\CreateCourses`.
- API/OpenAPI: `docs/api/academics.json`.

## 3. Stakeholders and Users

- Academic administrators: manage programs, semesters, and course catalogs.
- Course editors/instructors: maintain course metadata, templates (ILOs, time/mark allocations), and prerequisites.
- External systems: consume published academic data through REST endpoints.
- System administrators: enforce permissions and monitor audit logs.

## 4. Actors, Roles, and Permissions

- Dashboard access requires `auth`.
- Program overview: `permission:user.access.academic`.
- Semesters: `permission:user.access.academic.semester`.
- Courses: `permission:user.access.academic.course`.

## 5. Functional Requirements

- F1 Program Landing
  - Provide dashboard entry at `dashboard/academic_program` with breadcrumbs to semesters and courses.
- F2 Semester Management
  - Create/edit/delete semesters with `title`, `version` (integer curriculum revision), `academic_program`, optional `description`, unique `url`, and ownership metadata.
  - List semesters per academic program; cascade delete related courses when a semester is removed (database cascade configured).
  - Mark latest curriculum using highest `version` per `title` (per model accessor).
- F3 Course Management
  - Create/edit/delete courses with unique `code`, `semester_id`, `academic_program`, `version`, `name`, `credits`, `type`, `content`, JSON fields (`objectives`, `time_allocation`, `marks_allocation`, `ilos`, `references`), optional `faq_page`, and ownership metadata.
  - Update courses through Livewire flow `CreateCourses::update` to keep validation and prerequisite updates consistent.
  - Enforce referential integrity: courses belong to a semester; prerequisites stored in `course_prerequisites` link to other courses.
  - Surface taxonomy-backed templates for ILO/time/marks allocations via `TaxonomyTerm` lookups (`course_ilos`, `time_allocations`, `mark_allocations`).
- F4 Publishing and Visibility
  - Academic dashboards remain authenticated; public academic endpoints expose stored course/semester data without draft-stage separation (no publish flag in schema).
  - Protect deletion when dependent prerequisites exist via relational constraints.
- F5 Continuity with Phase 1
  - News/events/announcements continue unchanged; dashboard navigation keeps breadcrumb patterns established in phase 1.
- F6 API and Integration
  - REST endpoints documented in `docs/api/academics.json` return academic program data; downstream consumers rely on stable URLs and course codes.

## 6. Data Requirements

- Maintain uniqueness for semester `url` and course `code`.
- Persist created/updated user references for semesters and courses.
- Preserve prerequisite links in `course_prerequisites` with cascading delete on course removal.
- Cache course template data for ILO/marks/time allocations for 1 hour (`Course::CACHE_DURATION`).

## 7. External Interfaces

- Web UI: Blade dashboards plus Livewire flows for courses; breadcrumbs across academic routes.
- API: REST endpoints for academic data (see OpenAPI spec).
- Storage: reuses existing file system setup; no new storage types introduced.

## 8. Non-functional Requirements

- Data quality: inline validation for uniqueness (course code, semester URL) and required numeric fields (credits/version).
- Performance: dashboard lists respond in <2s P95 for typical catalogs; cached templates reduce taxonomy lookups.
- Security: RBAC enforced via permission middleware; CSRF protection on forms; audit logging enabled via `LogsActivity`.
- Availability: 99% monthly for academic browsing; maintenance mode available for migrations.
- Maintainability: PSR-12/Prettier formatting; CI coverage includes academic controllers/Livewire components.

## 9. Constraints and Assumptions

- Relies on existing Laravel auth; no anonymous edits.
- Database migrations (including enum-to-string and version integer updates) must run before deployment.
- Taxonomy terms required for ILO/time/marks templates must exist (`course_ilos`, `time_allocations`, `mark_allocations` codes).

## 10. Acceptance Criteria

- Administrators with academic permissions can create, edit, and delete semesters and courses; course updates persist via Livewire flow.
- Courses link to semesters and prerequisites; deleting a course removes dependent prerequisite rows automatically.
- Course codes remain unique, and semester URLs stay unique per schema constraints.
- Public academic endpoints surface the stored academic data without exposing draft-only content.
