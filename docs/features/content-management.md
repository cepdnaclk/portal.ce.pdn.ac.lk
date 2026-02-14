# Content Management: News, Events, Announcements

## Overview

The content management domain handles News, Events, and Announcements. News and Events share a common base model with gallery support and are exposed via API v1 and v2. Announcements are time-bound notices with tenant and area targeting.

## Shared content model (News/Event)

`App\Domains\ContentManagement\Models\BaseContent` provides:

- `gallery()` polymorphic relation to `GalleryImage`.
- `coverImage()` relation with a convenience `cover_image` accessor.
- `thumbURL()` helper to return a cover thumbnail or fallback image.
- `user()`/`author()` and `tenant()` relationships.
- Activity logging via `spatie/laravel-activitylog`.

### News model

`App\Domains\ContentManagement\Models\News` fields include:

- `title`, `url`, `description`
- `image`, `link_url`, `link_caption`
- `published_at`, `tenant_id`, `enabled`

### Event model

`App\Domains\ContentManagement\Models\Event` fields include:

- `title`, `url`, `description`
- `start_at`, `end_at`, `location`
- `event_type` (array of taxonomy-coded values)
- `published_at`, `tenant_id`, `enabled`

`Event::eventTypeMap()` loads a taxonomy term with code `events` and maps the child metadata `key` to event names. The mapping is cached for 1 hour.

## Announcements

`App\Domains\Announcement\Models\Announcement` fields include:

- `area`: `frontend`, `backend`, or `both`
- `type`: `info`, `danger`, `warning`, or `success`
- `message`, `enabled`, `starts_at`, `ends_at`, `tenant_id`

Scopes in `AnnouncementScope`:

- `enabled()` only enabled records.
- `forTenant()` and `forTenants()` tenant filtering.
- `forArea()` resolves `both` entries for frontend/backend.
- `inTimeFrame()` returns announcements active at the current time.

## Backend management routes

Admin editing routes live under:

- `routes/backend/news.php`
- `routes/backend/event.php`
- `routes/backend/announcements.php`

Routes use permission middleware and `tenant.access` to ensure editors can only modify allowed tenants. The gallery management UI is reachable from the News/Event edit screens.

## API endpoints

### News (v1)

- `GET /api/news/v1` (default tenant, enabled only)
- `GET /api/news/v1/{id}`

### News (v2)

- `GET /api/news/v2/{tenant_slug}`
- `GET /api/news/v2/{tenant_slug}/{id}`

### Events (v1)

- `GET /api/events/v1`
- `GET /api/events/v1/upcoming`
- `GET /api/events/v1/past`
- `GET /api/events/v1/{id}`

Query params:

- `event_type` (human-readable name; mapped to the taxonomy-backed ID)

### Events (v2)

- `GET /api/events/v2/{tenant_slug}`
- `GET /api/events/v2/{tenant_slug}/upcoming`
- `GET /api/events/v2/{tenant_slug}/past`
- `GET /api/events/v2/{tenant_slug}/{id}`

### Announcements (v2)

- `GET /api/announcements/v2/{tenant_slug}`

Query params:

- `area` (frontend/backend/both)

## API payload structure

News and Event responses include:

- `author` from `author_id` (`name`, `email`, `profile_url`).
- `image` using the cover thumbnail URL.
- `gallery` when the gallery feature is enabled.

Announcements return the active time window (`starts_at`, `ends_at`) and styling keys (`area`, `type`).
