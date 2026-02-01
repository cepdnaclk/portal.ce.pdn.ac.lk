# Taxonomy System

## Overview

The taxonomy system is a graph data store that provides structured vocabularies and metadata for the portal, powering various data structures used to manage CE domain data. It supports nested terms, typed properties, and API access for public taxonomies.

## Taxonomy model

`App\Domains\Taxonomy\Models\Taxonomy` stores:

- `code`, `name`, `description`
- `properties` (JSON array of property descriptors)
- `visibility` (public or hidden to the APIs)

Property types (see `Taxonomy::$propertyType`):

- Primary: `string`, `email`, `integer`, `float`, `date`, `datetime`, `boolean`, `url`
- Secondary: `file`, `page`, `list`, `taxonomy_term`

## Taxonomy terms

`App\Domains\Taxonomy\Models\TaxonomyTerm` supports:

- Nested terms via `parent_id` and `children()`.
- `metadata` JSON referencing the taxonomy properties.
- Recursive deletion of children in the model boot hook.

### Metadata formatting

`formatted_metadata` transforms typed values for API output:

- `file` -> download URL (`download.taxonomy-file`).
- `page` -> HTML page URL (`download.taxonomy-page`).
- `list` -> embedded `TaxonomyListItemResource`.
- `taxonomy_term` -> embedded `TaxonomyTermResource`.
- `datetime` -> ISO 8601 string.

The validator `TaxonomyTermMetadataValidator` enforces type-aware rules for each property.

## Lists, pages, and files

### Taxonomy lists

`TaxonomyList` stores an ordered JSON array of items with a `data_type` (string/date/url/email/file/page). Items are stored as JSON and are returned in API responses when referenced by terms.

### Taxonomy pages

`TaxonomyPage` stores HTML content and is served via `GET /taxonomy-page/{slug}`. HTML content is sanitized to a safe subset of tags before saving.

### Taxonomy files

`TaxonomyFile` stores file metadata and is served via `GET /taxonomy/{file_name}.{extension}`. Supported extensions are limited to `pdf`, `jpg`, `jpeg`, `png`, `webp`.

## API endpoints

All taxonomy API routes are under `routes/api.php`:

- `GET /api/taxonomy/v1` lists visible taxonomies.
- `GET /api/taxonomy/v1/{taxonomy_code}` returns a full taxonomy with terms.
- `GET /api/taxonomy/v1/term/{term_code}` returns a single term with formatted metadata.

Visibility is enforced: non-visible taxonomies return a `404` response.
