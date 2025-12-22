# portal.ce.pdn.ac.lk — Software Requirements Specification: Phase 1

## 1. Purpose and Scope

- **Purpose**: Define the initial public portal capabilities delivered in phase 1.
- **Scope**: Public-facing content publishing (news, events, announcements), basic intranet entry point, localization switcher, user authentication, and editor-facing dashboards.

## 2. Release Context

- v0.1.0 — Layout changes; establishes the base Laravel/Vue portal shell and UI structure.
- v1.0.0 — Manage News and Events; adds core content CMS for news/events.
- v1.1.0 — Bug fixes and improvements.
- v1.2.0 — Hardens first release series; stabilizes content workflows with additional features (Google Sign in, Preview Pages) and bug fixes.

## 3. Stakeholders and Users

- Public visitors: consume published news, events, announcements.
- Content editors (news/events/announcements): create, edit, preview, publish, and maintain assets.
- Administrators: manage users/roles, approve content, and configure localization.
- Intranet users: authenticated student/staff accessing dashboard entry points.

## 4. System Features and Functional Requirements

- F1 Authentication & Access Control
  - Support user login/logout with Laravel auth; enforce role/permission checks on dashboard routes.
  - Password reset via email token; session timeout aligned with security policy.
- F2 Announcement Management
  - Create/edit/delete announcements with effective/expiry dates.
  - Publish/unpublish without deleting content; list announcements by recency.
- F3 News Management
  - Create/edit/delete news items with title, body, publish window, author, status (draft/published).
  - Upload and manage image galleries per news item (upload, reorder, set cover, delete).
  - Preview news before publish; public view routes for published items.
- F4 Event Management
  - Create/edit/delete events with scheduling metadata (start/end), venue, description, contacts.
  - Manage per-event image galleries (upload, reorder, set cover, delete).
  - Preview events before publish; public view routes for published events.
- F5 Dashboard & Navigation
  - Provide authenticated dashboard navigation for editors/admins; breadcrumbs for usability.
- F6 Content Publishing Rules
  - Only users with feature-specific permissions may create/update/delete content.
  - Prevent public access to draft/unpublished items; enforce published state checks on frontend.

## 5. Data Requirements

- Persist news, events, and announcements with timestamps and owner metadata.

## 6. External Interfaces

- Web UI: Laravel Blade for public site and dashboard forms.
- API: REST-style routes for public consumption of news/events (OpenAPI docs under `docs/api/*.json`).
- Storage: File system for thumbnail uploads; download endpoints expose sanitized paths.

## 7. Non-functional Requirements

- Availability: 99% monthly for public content; graceful error pages for outages.
- Performance: Public pages render in <2s P95 on campus network.
- Security: Enforce Role-Based Access Control (RBAC) on dashboard routes; validate file types/sizes.
- Usability: Accessible navigation with breadcrumbs; mobile-friendly layouts for public content.
- Maintainability: PSR-12 PHP, Prettier JS formatting; automated CI (Laravel CI badge) must pass.

## 8. Constraints and Assumptions

- Laravel framework with Vue 2 frontend; PNPM/Composer managed dependencies.
- Authentication uses session cookies; assumes HTTPS termination by deployment environment.
- Email delivery configured externally for password resets/notifications.
- Content editors are trained; no guest content creation.

## 9. Acceptance Criteria

- Editors can create, preview, publish, and retire news/events/announcements.
- Public visitors can view published items and assets without authentication (via APIs).
- Role/permission checks block unauthorized access to dashboard routes.
