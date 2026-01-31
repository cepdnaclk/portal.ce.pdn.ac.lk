# Articles

## Overview

- Articles are tenant-scoped content items with rich-text body, categories, optional gallery images, and embedded content images.
- Content authoring is done in the backend dashboard with a TinyMCE editor and a preview screen.
- API endpoints expose articles with author, categories, content images, and gallery payloads when enabled.

## Data Model & Storage

- Stored in the `articles` table with tenant ownership and author tracking.
- Key fields:
  - `title`, `content`, `published_at`
  - `categories_json` for comma-separated tags stored as JSON
  - `content_images_json` for rich-text embedded images
  - `gallery_json` for gallery metadata (kept in sync through gallery services)
  - `created_by` and `updated_by` for audit
- JSON fields are cast to arrays in the model to simplify backend/UI usage.

## Permissions & Tenancy

- Backend access is guarded by the `user.access.editor.articles` permission.
- Tenant selection is required on create/edit; if a user only has one tenant, it is auto-selected.
- All queries and API responses scope to the resolved tenant.

## Backend UI Workflow

- Create flow:
  - Enter title, select tenant, optionally add comma-separated categories.
  - Compose content in TinyMCE; embedded images upload directly from the editor.
  - On save, `published_at` is set and content HTML is sanitized.
- Edit flow:
  - Same fields as create, with current content and categories prefilled.
  - Rich-text editor preserves embedded images and updates the content image list.
  - When gallery is enabled, a “Manage Gallery” action is available from edit screens.
- Preview:
  - A standalone preview route renders the article content for review.
  - Reading time is derived from word count in the preview view.

## Rich Text & Content Images

- TinyMCE is configured with image upload support and strict URL handling to use absolute URLs.
- Image uploads are saved under the configured gallery disk and returned via a download route.
- The editor maintains a hidden `content_images_json` payload that tracks uploaded image metadata.
- On save, the system:
  - Filters the content image list to only images referenced in the HTML.
  - Deletes unused images from storage.
  - Stores the filtered list on the article for API and future edits.

## Gallery Integration

- Articles can have a full gallery alongside embedded content images.
- Gallery management (upload, cover selection, reorder) is available through dedicated routes.
- Article deletion removes both gallery images and content-embedded images.

## Validation & Sanitization

- Required fields: `title`, `content`, `tenant_id`.
- Categories are parsed from comma-separated input and normalized to unique values.
- Content HTML is sanitized to a safe allowlist of tags before persistence.
- Content image uploads enforce:
  - MIME type checks (JPEG only)
  - Size limits based on gallery configuration
  - Minimum dimension rules

## API Exposure

- API v1:
  - Uses a default tenant resolver; returns paginated results.
  - Supports list, single item, and category filter.
- API v2:
  - Tenant slug is required in the path; returns tenant-scoped data.
  - Supports list, single item, and category filter.
- Article resource payloads include:
  - Author metadata
  - Categories
  - Embedded content images (`id`, `url`)
  - Gallery items (when gallery is enabled)

## Logging & Audit

- Article create/update/delete actions log key identifiers and image changes.
- Content image upload logs tenant, user, and image identifiers.

## Key Routes (Backend & Download)

- Backend (dashboard):
  - `dashboard.article.index`, `dashboard.article.create`, `dashboard.article.store`
  - `dashboard.article.edit`, `dashboard.article.update`, `dashboard.article.delete`, `dashboard.article.destroy`
  - `dashboard.article.preview`
  - `dashboard.article.content-images.upload`
  - `dashboard.article.gallery.*`
- Download:
  - `download.article` serves embedded content images by filename.

## Configuration Touch-points

- Content image handling uses gallery settings for disk, size limits, and MIME types.
- Cache headers for content images are controlled by the gallery cache TTL configuration.
