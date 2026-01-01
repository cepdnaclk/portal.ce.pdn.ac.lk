# portal.ce.pdn.ac.lk — Software Requirements Specification: Phase 1

## 1. Purpose and Scope

- Purpose: Specify the initial public portal and editorial capabilities shipped in phase 1.
- Scope: Public-facing content (news, events, announcements), dashboard editing with galleries, localization switcher, authentication, and download endpoints reused by later phases.
- Out of scope: Academic program and taxonomy capabilities (introduced in later phases).

## 2. References

- Routes: `routes/backend/{news.php,event.php,announcements.php}`, `routes/web.php` (download/gallery and locale).
- Data models: `database/migrations/2024_06_22_200003_create_news_table.php`, `2024_06_27_150621_create_events_table.php`, `2024_10_09_114038_add_event_type_to_events_table.php`, `2020_05_25_021239_create_announcements_table.php`.
- API/OpenAPI: `docs/api/*.json` (public news/events endpoints).

## 3. Stakeholders and Users

- Public visitors: browse published news, events, and announcements.
- Content editors: manage announcements, news, and events (including galleries).
- Administrators: manage users/roles and enforce permissions.
- Intranet users: authenticated staff/students accessing dashboard entry points.

## 4. Actors, Roles, and Permissions

- Authenticated dashboard users gated by `auth` middleware.
- News editors: `permission:user.access.editor.news`.
- Event editors: `permission:user.access.editor.events`.
- Announcements: protected by dashboard auth; no additional permission middleware is defined in routes.

## 5. Functional Requirements

- F1 Authentication & Localization
  - Provide login/logout and password reset flows via Laravel auth.
  - Locale switching available at `lang/{lang}`; applies to public and dashboard views.
- F2 Announcement Management
  - Create/edit/delete announcements with `area`, `type`, `message`, `starts_at`, `ends_at`, and `enabled` flags.
  - Schedule visibility using start/end timestamps; deactivate via `enabled`.
- F3 News Management
  - Create/edit/delete news with `title`, `description`, `url` (unique slug), `image`, optional link (`link_url`, `link_caption`), `published_at`, `enabled`, and ownership metadata.
  - Preview draft news via dashboard preview route before publish.
  - Manage news galleries: upload (throttled by `gallery-uploads`), reorder, set cover, update captions, and delete images; public downloads served via `download/gallery/{path}`.
- F4 Event Management
  - Create/edit/delete events with `title`, `description`, `url`, `published_at`, `start_at`, `end_at`, `location`, `image`, optional link, `event_type` (JSON), `enabled`, and ownership metadata.
  - Preview draft events; enforce required start time and optional end time.
  - Manage event galleries with the same capabilities and throttling as news galleries.
- F5 Dashboard Navigation and UX
  - Breadcrumbs on dashboard routes for news/events/announcements.
  - Separate index, create, edit, delete, preview, and gallery management screens per module.
- F6 Publishing and Visibility Rules
  - Only users with module permissions can mutate news/events; dashboard auth is required for announcements.
  - Draft/disabled items stay hidden from public endpoints; downloads restricted to published/gallery assets only.

## 6. Data Requirements

- Persist announcements (`area`, `type`, `message`, `enabled`, `starts_at`, `ends_at`).
- Persist news records and associated gallery assets; keep creator timestamps and ownership.
- Persist event records with schedule metadata and galleries; retain creator timestamps and ownership.
- Enforce uniqueness on news `url`, event `url`, and gallery file paths.

## 7. External Interfaces

- Web UI: Blade/Vue dashboard forms for CRUD and gallery management; Blade public pages for published content.
  - Download endpoints: `download/gallery/{path}`; taxonomy downloads introduced later share the same group.
- API: REST-style public routes for news/events (see `docs/api/*.json`).
- Email: password reset delivery uses configured mail transport.

## 8. Non-functional Requirements

- Availability: public content available 99% monthly; graceful error pages for outages.
- Performance: public pages and gallery downloads respond in <2s P95 on campus network.
- Security: RBAC enforced via middleware; file uploads validated for type/size; gallery uploads throttled.
- Usability: mobile-friendly public pages; breadcrumbs and previews for editors.
- Maintainability: PSR-12 PHP and Prettier JS; CI (Laravel CI badge) must pass.

## 9. Constraints and Assumptions

- Laravel backend with Vue 2 assets; dependencies managed via Composer/PNPM.
- Session-cookie authentication; HTTPS termination handled by hosting environment.
- Storage: local filesystem with `php artisan storage:link`; filenames sanitized on upload.
- Editors are trained; no guest authoring.

## 10. Acceptance Criteria

- Editors can create, preview, publish, and retire announcements, news, and events via dashboard routes.
- Public visitors can view only enabled/published items and download gallery assets through sanctioned routes.
- Permissions block unauthorized dashboard access for news/events; announcements remain within authenticated dashboard access.
