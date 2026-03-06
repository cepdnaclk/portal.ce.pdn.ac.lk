# Authentication and Access Control

## Overview

The authentication stack covers frontend login, registration, password recovery, email verification, and optional social login. Backend administration includes user/role management with permission-based access and tenant-aware restrictions.

Key route files:

- `routes/frontend/auth.php` for public/authenticated flows.
- `routes/backend/auth.php` for admin user and role management.

## Frontend authentication flows

The public auth routes provide:

- Login/logout and registration pages.
- Password reset email and token-based reset.
- Email verification with resend support.
- Password confirmation and password change for verified users.
- Two-factor authentication (2FA) enable/disable and recovery code regeneration.
- Social login redirects and callbacks (`login/{provider}`).

## Two-factor authentication (2FA)

2FA routes live under `account/2fa/*` and are guarded by a dedicated middleware:

- `2fa:disabled` allows access to the enable flow.
- `2fa:enabled` gates recovery/disable endpoints.

The middleware `App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus` enforces whether a user must have 2FA enabled/disabled before accessing a route. In the backend, 2FA enforcement can be toggled via `boilerplate.access.user.admin_requires_2fa`.

## Password expiry

Routes under `password/expired` allow a user to reset an expired password. The `password.expires` middleware is applied to authenticated routes to redirect expired accounts to the update flow.

## Backend user and role management

Admin-only routes in `routes/backend/auth.php` cover:

- User CRUD, deactivation/reactivation, and soft-delete restore.
- User session clearing and password resets from the admin UI.
- Role CRUD and permission assignment (roles are attached to users and tenants).

Authorization is enforced by middleware in `App\Domains\Auth\Http\Middleware` (admin checks, user type checks, and session validation).

## Permission management guidelines

Use roles as the primary mechanism for access control and keep user-specific permissions to exceptions.

How to assign permissions:

- Role permissions: Navigate to `Role Management`, create or edit a role, then choose permissions from the categorized list before saving.
- User role assignment: In `User Management`, edit a user and attach one or more roles under the roles section.
- User-specific permissions: In the same user edit screen, use additional permissions only when a user needs a temporary or exceptional capability.
- Tenant scoping: When creating or editing a role, assign allowed tenants to limit where the role applies.

Best practices:

- Apply least privilege: start with the minimum set of permissions and expand only when needed.
- Prefer role updates over direct user permissions to keep access consistent and auditable.
- Review assigned users per role/tenant regularly using the dedicated assignment views.
- Align permission names with feature areas and routes to keep the permission matrix easy to reason about.
- Remove unused roles and permissions after decommissioning features.

## Domain events and observers

User and role lifecycle events are emitted under `App\Domains\Auth\Events` and handled by listeners in `App\Domains\Auth\Listeners`. User changes are also observed by `App\Domains\Auth\Observers\UserObserver`, allowing audit logging and downstream behavior.

## Related models

- `App\Domains\Auth\Models\User`
- `App\Domains\Auth\Models\Role`
- `App\Domains\Auth\Models\Permission`
- `App\Domains\Auth\Models\PasswordHistory`

These models support relationships to tenants, roles, and permissions (see the tenancy documentation for how tenant access is resolved).
