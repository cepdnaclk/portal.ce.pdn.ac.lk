# Multi-Tenant Architecture

## Overview

The portal supports multiple tenants (departments or sites) and resolves the active tenant based on request inputs, URL, or host. Tenant resolution is used in both API endpoints and backend access control.

## Tenant model

`App\Domains\Tenant\Models\Tenant` includes:

- `slug`, `name`, `url`, `description`
- `is_default` flag (used as the fallback tenant)

Relationships:

- `users` and `roles` (pivot tables `tenant_user` and `tenant_role`)
- `news`, `events`, and `announcements`

### Default tenant

`Tenant::default()` returns the tenant with `is_default = true` and is used when explicit tenant resolution fails.

## Tenant resolution flow

`App\Domains\Tenant\Services\TenantResolver` resolves tenants in this order:

1. Route parameter `tenant_slug` or `tenant_id` (API v2 routes or query/body).
2. Route model binding for `News`, `Event`, or `Announcement`.
3. Request host match against the tenant `url` host.
4. Default tenant fallback.

This logic is applied via `resolveFromRequest()` and is also used directly in API controllers.

## Access control middleware

The `tenant.access` middleware (`App\Domains\Tenant\Http\Middleware\TenantAccess`) applies tenant authorization:

- Resolves a tenant using the resolver flow.
- Attaches the tenant to the request (`$request->attributes->set('tenant', $tenant)`).
- If the user is not a super admin and the request explicitly targets a tenant, access is allowed only when the user has tenant access.

If no tenant can be resolved, the middleware returns a 404 or JSON error.

## Backend tenant management

Tenant CRUD routes live under `routes/backend/tenants.php` and are handled by `App\Domains\Tenant\Http\Controllers\Backend\TenantController`.

## API behavior

API v2 endpoints use `tenant_slug` and will return a `404` for unknown tenants. API v1 endpoints use the default tenant (if present).
