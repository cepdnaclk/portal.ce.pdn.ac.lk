# portal.ce.pdn.ac.lk — Software Requirements Specification: Phase 2

## 1. Purpose and Scope

- Purpose: Define requirements for the Course Management System introduced in phase 2.
- Scope: Academic program administration (semesters, courses), prerequisite tracking, and continued support for Phase 1 content modules under unified authentication and permissions.
- Out of scope: Taxonomy-driven content and history logging (Phase 3).

## 2. Release Context

- v2.0.0 — Course Management System; adds academic program, semester, and course lifecycle management.
- v2.1.0 — Course Management enhancements; improves workflows and data quality.
- v2.1.1 — Hotfix: prerequisite semester title; corrects prerequisite metadata handling.

## 3. Stakeholders and Users

- Academic Administrators: manage programs, semesters, and course catalogs.
- Course editors/instructors: maintain course metadata and prerequisites.
- External Sub-systems: retrieve published academic program details through APIs.
- System administrators: manage roles/permissions and ensure data integrity.

## 4. System Features and Functional Requirements

- F1 Access Control
  - Enforce academic permissions (`user.access.academic`, `user.access.academic.semester`, `user.access.academic.course`) on dashboard routes.
- F2 Academic Program Overview
  - Provide dashboard entry summarizing program structure.
  - Link to semesters and courses with breadcrumb navigation.
- F3 Semester Management
  - Create/edit/delete semesters with name, code, sequence, academic year, publish state.
  - Validate uniqueness of semester identifiers per program.
- F4 Course Management
  - Create/edit/delete courses with code, title, credit/contact hours, description, delivery mode.
  - Assign courses to semesters; update existing course records via Livewire flow.
  - Define prerequisites (course and/or semester level) with validation; display readable prerequisite titles.
- F5 Publishing and Visibility
  - Allow draft vs published states; only published courses/semesters visible to public endpoints.
  - Prevent deletion when dependent records exist (e.g., courses linked to semesters/prerequisites).
- F6 Content Continuity
  - Preserve Phase 1 CMS features (news/events/announcements) under existing permissions and dashboards.
- F7 API and Integration
  - Expose REST-style endpoints for academic program data (OpenAPI under `docs/api/academics.json`).
  - Ensure download endpoints continue to serve media securely.

## 5. Data Requirements

- Persist program, semester, and course entities with timestamps and owner metadata.
- Maintain referential integrity for course-semester and prerequisite relationships.
- Provide migration scripts for prerequisite schema updates (covered via v2.1.1 hotfix).

## 6. External Interfaces

- Web UI: Laravel Blade dashboard forms for academic entities.
- API: API routes surfacing academic program JSON; secured dashboard routes for mutations.
- Storage: Existing file storage for media remains available; no new external systems introduced.

## 7. Non-functional Requirements

- Data quality: Unique course codes; validation errors shown inline; prerequisites must reference published or draft courses only.
- Performance: Dashboard list views load in <2s P95 for 200 courses; API responses paginated.
- Security: RBAC enforced on all academic routes; CSRF protection on form submissions.
- Availability: 99% monthly for academic browsing; maintenance mode available for migrations.
- Maintainability: PSR-12/Prettier formatting; automated CI must cover academic controllers and tests.

## 8. Constraints and Assumptions

- Uses existing Laravel auth; no anonymous course editing.
- Database migrations run prior to deployment; rollback path documented via Laravel migrations.
- Course prerequisite terminology follows department standards; localization keys provided as needed.

## 9. Acceptance Criteria

- Admins can create semesters and courses, attach prerequisites, and publish them.
- Public users can view published program/semester/course details without seeing drafts.
- Hotfix behavior: prerequisite titles display correctly and align with stored relations.
- RBAC prevents users without academic permissions from accessing academic dashboards.
