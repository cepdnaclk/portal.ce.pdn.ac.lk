# portal.ce.pdn.ac.lk — Software Requirements Specification: Phase 3

## 1. Purpose and Scope

- Purpose: Define requirements for the Taxonomy Integration introduced in phase 3.
- Scope: Taxonomy data/term/page/file/list management, permissions for taxonomy roles, download endpoints, aliasing, and audit/history logging.
- Out of scope: New course features (Phase 2) and non-taxonomy feature development beyond maintenance.

## 2. Release Context

- v3.0.0 — Taxonomy Integration; introduces taxonomy data model and dashboards.
- v3.1.0/v3.1.1 — Taxonomy permissions and hotfix; tightens access control, introduce a better WYSIWYG editor for News and Event pages.
- v3.2.0 — Taxonomy Files integration support, and additional UI enhancements.
- v3.3.0 — Taxonomy Pages, history logging and taxonomy feature integration for News and Event configs.
- v3.3.1 — Hotfix: Adding missing prerequisites into course list API.
- v3.3.2 — Hotfix: BS4 integration and fix for Gmail Login.
- v3.3.3 — Hotfix: Manage permission for Taxonomy Files and Pages.
- v3.3.4 — Hotfix: History logging for Taxonomy Files and Pages.
- v3.4.0 — Taxonomy Term management enhancements and refined Role managements
- v3.4.1 — Improved Livewire tables, fixed data length for Taxonomy items and API improvements on Taxonomy endpoints.
- v3.4.2 — Taxonomy Lists feature, Gallery feature and API improvements and proper documentations.
- v3.4.3 — Added SRS documents for phase 1-3.

## 3. Stakeholders and Users

- Taxonomy administrators/editors: curate taxonomy structures, terms, files, and pages.
- External Sub-systems: read/download published taxonomy content and media.
- System administrators: manage permissions, audit logs, and operational health.

## 4. System Features and Functional Requirements

- F1 Access Control
  - Enforce fine-grained permissions: taxonomy data editor/viewer, taxonomy file editor/viewer, taxonomy page editor/viewer.
  - Middleware must block unauthorized access and allow downloads only when permissions permit or when explicitly exempted (public downloads).
- F2 Taxonomy Data Management
  - Create/edit/delete taxonomy definitions with name, code, description, and publishing state.
  - Provide alias resolution routes for taxonomy code and term code lookup.
  - Maintain history views per taxonomy (timeline of changes).
- F3 Taxonomy Term Management
  - Create/edit/delete terms under a taxonomy with metadata (code, name, description, ordering).
  - View term history; enforce referential integrity when deleting terms with children or attachments.
- F4 Taxonomy Files
  - Upload, view, edit, and delete taxonomy files with metadata (file name, description, taxonomy association).
  - Download endpoint with path validation and throttling as configured.
  - Record change history for file metadata/versions.
- F5 Taxonomy Pages
  - Create/edit/delete taxonomy-linked pages (rich content) with slug-based routing.
  - Download/export HTML pages via dedicated endpoint.
  - Track page history and expose viewable logs for editors.
- F6 Navigation and UX
  - Dashboard landing for taxonomy with breadcrumbs; list views for taxonomy, terms, files, and pages.
  - Provide redirects/aliases to ease navigation from codes to detail pages.
- F7 Integration and Backward Compatibility
  - Preserve Phase 1/2 modules (news/events/announcements, academic programs) without breaking routes.
  - Refreshed API documentation is available as GitHub-hosted pages for documentation requirements.

## 5. Data Requirements

- Persist taxonomy, term, page, file, and list entities with timestamps, creator/updater, and version/history records.
- Store file binaries in configured storage with sanitized filenames; maintain mapping to taxonomy records.
- Maintain slug/code uniqueness within a taxonomy scope; log audit events for changes.

## 6. External Interfaces

- Web UI: Laravel Blade dashboard for taxonomy CRUD, history views, and downloads.
- API: REST-style taxonomy endpoints (OpenAPI spec available at `docs/api/taxonomy.json`).
- Download endpoints with middleware exemptions for public access for files and images.

## 7. Non-functional Requirements

- Security: Strict RBAC across taxonomy modules; middleware coverage for downloads; input validation on uploads (size/type).
- Auditability: History views must reflect create/update/delete events for taxonomy entities; logs retained per retention policy.
- Performance: Taxonomy list views load in <2s P95 for 500 entries; downloads stream efficiently with chunked responses.
- Availability: 99% monthly for taxonomy browsing and downloads; maintenance mode available for migrations.
- Maintainability: PSR-12/Prettier standards; automated CI covers taxonomy controllers, middleware, and history logging.

## 8. Constraints and Assumptions

- Uses existing Laravel auth and storage configuration.
- Download routes may bypass certain permissions only when explicitly configured via middleware exemptions.
- History logging relies on database support for version/audit tables; migrations applied before release.

## 9. Acceptance Criteria

- Authorized editors can create taxonomies, terms, files, pages, and lists; history views show change events.
- Unauthorized users are blocked from editor routes; viewer permissions allow read-only access.
- Public downloads for permitted taxonomy pages/files work via alias/slug routes without exposing protected content.
