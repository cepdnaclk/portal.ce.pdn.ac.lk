# portal.ce.pdn.ac.lk — Software Requirements Specification: Phase 3

## 1. Purpose and Scope

- Purpose: Define the taxonomy-driven extensions delivered in phase 3.
- Scope: Dashboard CRUD for taxonomies, terms, files, pages, and lists; download/alias endpoints; history logging; permission hardening; and integration with prior phases (content + academics).
- Out of scope: New academic/course features beyond taxonomy reuse.
- Multi-tenant support: Inherits tenant context from authenticated dashboard users; taxonomy data is tenant-specific.

## 2. References

- Routes: `routes/backend/taxonomy.php`, download routes in `routes/web.php`.
- Data models: `database/migrations/2024_10_11_120037_create_taxonomies_table.php`, `2025_06_17_084036_add_visibility_to_taxonomies_table.php`, `2024_10_12_085403_create_taxonomy_terms_table.php`, `2025_05_17_220220_create_taxonomy_files_table.php`, `2025_06_23_171806_create_taxonomy_pages_table.php`, `2025_10_08_000000_create_taxonomy_lists_table.php`, `2025_10_07_210632_update_taxonomy_code_length.php`.
- Controllers: `App\Http\Controllers\Backend\{TaxonomyController,TaxonomyTermController,TaxonomyFileController,TaxonomyPageController,TaxonomyListController}`.
- API/OpenAPI: `docs/api/taxonomy.json`.

## 3. Stakeholders and Users

- Taxonomy administrators/editors: manage structures, terms, files, pages, and lists.
- External consumers: fetch taxonomy data and download published files/pages.
- System administrators: govern permissions and audit history.

## 4. Actors, Roles, and Permissions

- Authenticated dashboard users with permission middleware:
  - Data: `user.access.taxonomy.data.editor` or `user.access.taxonomy.data.viewer`.
  - Files: `user.access.taxonomy.file.editor` or `user.access.taxonomy.file.viewer`.
  - Pages: `user.access.taxonomy.page.editor` or `user.access.taxonomy.page.viewer`.
  - Lists: `user.access.taxonomy.list.editor` or `user.access.taxonomy.list.viewer`.
- Download exemptions: taxonomy file/page downloads remove permission middleware for public access where configured.

## 5. Functional Requirements

- F1 Taxonomy Data
  - Create/edit/delete taxonomies with `code`, `name`, optional `description`, JSON `properties`, and `visibility` flag; history logged via `LogsActivity`.
  - Alias routes resolve taxonomy by `code` and term by `code` (`/taxonomy/alias/{code}`, `/taxonomy/alias/term/{code}`).
  - Breadcrumbed views for index, view, history, create, edit, delete.
- F2 Terms
  - Create/edit/delete terms under a taxonomy with unique `code`, `name`, `metadata` (JSON), optional `parent_id`, and ownership metadata.
  - Maintain history views; enforce referential integrity for parent/child relationships.
  - Redirect to term via alias route.
- F3 Files
  - Upload/view/edit/delete taxonomy files with `file_name`, unique `file_path`, optional `taxonomy_id`, JSON `metadata`, and ownership metadata.
  - Download endpoint at `download/taxonomy/{file_name}.{extension}` available without permission middleware; must validate path before serving content.
- F4 Pages
  - Create/edit/delete taxonomy-linked pages with unique `slug`, HTML content (`html`), optional `metadata`, and ownership metadata.
  - Download/export at `download/taxonomy-page/{slug}` without permission middleware; history view available.
- F5 Lists
  - Create/edit/delete taxonomy lists with `name`, optional `taxonomy_id`, `data_type`, JSON `items`, and ownership metadata.
  - Manage list items through dedicated manage route (`taxonomy-lists/manage/{taxonomyList}`).
- F6 Navigation and UX
  - Dashboard landing pages for taxonomies, terms, files, pages, and lists with breadcrumbs.
  - Alias redirects simplify navigation from codes to detail screens.
- F7 Integration and Backward Compatibility
  - Downloads for taxonomy assets coexist with gallery downloads and academic/content routes.
  - Taxonomy terms power course templates (ILOs/allocations) without breaking phase 1/2 functionality.

## 6. Data Requirements

- Enforce uniqueness: taxonomy `code`, term `code`, page `slug`, file `file_path`.
- Persist created/updated user references and timestamps across taxonomy entities.
- Store file binaries in configured storage path; map to taxonomy records for retrieval.
- Track history/audit data through `LogsActivity` traits.

## 7. External Interfaces

- Web UI: Blade dashboards for taxonomy CRUD, history, and item management; breadcrumbs on all screens.
- API: REST-style taxonomy endpoints (see OpenAPI).
- Downloads: public endpoints under `download/` for taxonomy files/pages with permission exemptions.

## 8. Non-functional Requirements

- Security: permission middleware per module; download exemptions limited to explicit routes; upload validation for size/type.
- Auditability: history views for taxonomies, terms, files, pages, and lists; activity logs capture changes.
- Performance: taxonomy list and history views respond in <2s P95 for typical datasets; downloads stream efficiently.
- Availability: 99% monthly for taxonomy browsing and downloads; maintenance mode available during migrations.
- Maintainability: PSR-12/Prettier standards; CI coverage includes taxonomy controllers, middleware, and history logging.

## 9. Constraints and Assumptions

- Relies on existing Laravel auth and storage configuration.
- Permission exemptions apply only to download routes declared without middleware in `routes/web.php`.
- Database migrations (including code length and visibility updates) must be applied before release.

## 10. Acceptance Criteria

- Authorized editors/viewers can navigate, create, and update taxonomies, terms, files, pages, and lists; history views display audit entries.
- Unauthorized users are blocked from dashboard routes; download exemptions work only on the declared public download endpoints.
- Public downloads for taxonomy pages/files resolve via slug/filename and return the stored asset without exposing protected content.
