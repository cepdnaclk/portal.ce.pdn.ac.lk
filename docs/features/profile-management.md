# Profile Management

The portal includes a multi-profile Profile Management module that is independent from portal user accounts while still supporting linked-account workflows, role-based self-service, and dashboard-level administration.

## Overview

- One user can own multiple profiles of different types.
- Profiles can exist without a linked portal account.
- Linked profiles are shown on `/intranet/account`.
- Self-service management is available at `/dashboard/my-profiles`.
- Admin and Profile Manager management is available at `/dashboard/profiles`.
- Change history is available for both self-service and admin views.
- OpenAPI reference: [`docs/openapi/profiles.yaml`](../openapi/profiles.yaml)

## Supported Profile Types

- `UNDERGRADUATE_STUDENT`
- `POSTGRADUATE_STUDENT`
- `ACADEMIC_STAFF`
- `TEMPORARY_ACADEMIC_STAFF`
- `ACADEMIC_SUPPORT`
- `EXTERNAL`

## Main Capabilities

- Multi-profile ownership keyed by user and email.
- Independent profiles with nullable `user_id`.
- Shared identity field synchronization across linked profiles.
- Sync of `preferred_long_name` into `users.name` for linked accounts.
- Profile completeness scoring stored in session after login and profile updates.
- Activity-log-backed history pages for create, update, and delete operations.
- Self-service profile creation based on assigned roles.
- Admin filtering and search for profile management.
- Dedicated delete confirmation flow before destructive actions.

## Data Model Notes

The profile schema is defined in the existing migration:

- [`database/migrations/2026_04_06_000001_create_profiles_table.php`](../../database/migrations/2026_04_06_000001_create_profiles_table.php)

Important fields:

- `email`
- `user_id`
- `type`
- `full_name`
- `name_with_initials`
- `preferred_short_name`
- `preferred_long_name`
- `reg_no`
- `honorific`
- `current_position`
- `department`
- `phone_number`
- `biography`
- `current_affiliation`
- `previous_affiliations`
- social/profile links
- `review_status`
- `created_by`
- `updated_by`

Constraints and behaviors:

- `(email, type)` is unique.
- `user_id`, `created_by`, and `updated_by` are nullable foreign keys to `users`.
- `reg_no` uses the format `E/YY/XXX`.
- `review_status` defaults to `APPROVED`.

## Setup

Run the migration and seed the role/permission package as usual:

```bash
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\Roles\\ProfileManagerRoleSeeder
```

To import profile records from CE Peoples:

```bash
php artisan db:seed --class=Database\\Seeders\\SyncProfilesSeeder
```

## Roles and Permissions

The module introduces a `Profile Manager` role with the following permissions:

- `user.access.profiles.view`
- `user.access.profiles.edit`
- `user.access.profiles.delete`

Seeder:

- [`database/seeders/Roles/ProfileManagerRoleSeeder.php`](../../database/seeders/Roles/ProfileManagerRoleSeeder.php)

Behavior:

- The seeded admin user is assigned the `Profile Manager` role by default.
- Admin users still inherit full platform access where applicable.
- UI actions are conditionally rendered based on permissions.

## Configuration

All module defaults are defined in:

- [`config/profiles.php`](../../config/profiles.php)

Key sections:

- `required_fields`
  Controls completeness scoring and request-level required fields.
- `sync.students_url`
  CE Peoples student source endpoint.
- `sync.staff_url`
  CE Peoples staff source endpoint.
- `sync.timeout`
  HTTP timeout for profile synchronization.
- `shared_identity_fields`
  Fields synchronized across all linked profiles of the same user.
- `image`
  Profile image storage and validation rules.

Current image rules:

- disk: `public`
- directory: `profiles`
- mime types: `image/jpeg`
- max size: `2048 KB`
- aspect ratio: between `3:4` and `1:1`

## Self-Service Flow

Self-service routes:

- `GET /dashboard/my-profiles`
- `GET /dashboard/my-profiles/create?type=...`
- `POST /dashboard/my-profiles`
- `GET /dashboard/my-profiles/{profile}`
- `PATCH /dashboard/my-profiles/{profile}`
- `GET /dashboard/my-profiles/{profile}/history`

Rules:

- A user only sees profile types allowed by their roles.
- Missing allowed profile types are surfaced as create actions.
- Existing linked profiles are managed through a Livewire table.
- Shared identity fields are auto-filled from an existing linked profile when available.

Role-to-profile mapping:

- `Lecturer` -> `ACADEMIC_STAFF`
- `Student` -> `UNDERGRADUATE_STUDENT`
- `Postgraduate Student` -> `POSTGRADUATE_STUDENT`
- `Temporary Academic Staff` -> `TEMPORARY_ACADEMIC_STAFF`
- `Academic Support Staff` -> `ACADEMIC_SUPPORT`
- `External Collaborator` -> `EXTERNAL`

## Admin Flow

Admin/profile-manager routes:

- `GET /dashboard/profiles`
- `GET /dashboard/profiles/create`
- `POST /dashboard/profiles`
- `GET /dashboard/profiles/{profile}/edit`
- `PATCH /dashboard/profiles/{profile}`
- `GET /dashboard/profiles/{profile}/history`
- `GET /dashboard/profiles/{profile}/delete`
- `DELETE /dashboard/profiles/{profile}`

Admin management uses a Livewire table with:

- search by name or email
- filter by profile type
- linked-vs-independent filtering
- edit action
- history action
- delete-confirmation navigation

## Account Integration

The account page is still served from:

- `GET /intranet/account`

Enhancements on that page:

- linked profiles list
- profile types
- completeness scores
- direct links to edit and history pages

The user dropdown also includes a `My Profile` shortcut to:

- `/dashboard/my-profiles`

## Synchronization and Linking

External import seeder:

- [`database/seeders/SyncProfilesSeeder.php`](../../database/seeders/SyncProfilesSeeder.php)

Source endpoints:

- students: `https://api.ce.pdn.ac.lk/people/v1/students/all/`
- staff: `https://api.ce.pdn.ac.lk/people/v1/staff/all/`

Seeder behavior:

- idempotent via `updateOrCreate`
- record-level failure isolation
- CLI error output without aborting the whole run
- file logging to `storage/logs/profile-sync.log`
- explicit field mapping into the `Profile` model

User registration linking:

- Listener: [`app/Listeners/UserRegisteredListener.php`](../../app/Listeners/UserRegisteredListener.php)
- Matches existing profiles by email.
- Links them to the newly registered user.
- Does not auto-create a new profile if none exists.
- Syncs roles from linked profile types.

Login completeness refresh:

- Listener: [`app/Listeners/UserLoginListener.php`](../../app/Listeners/UserLoginListener.php)
- Recomputes per-profile completeness and stores summary data in session.

## Shared Field Synchronization

Shared fields are configured in `config/profiles.php` under `shared_identity_fields`.

Current synced fields:

- `full_name`
- `name_with_initials`
- `preferred_short_name`
- `preferred_long_name`
- `gender`
- `honorific`
- `profile_website`
- `profile_linkedin`
- `profile_github`
- `profile_researchgate`
- `profile_google_scholar`
- `profile_orcid`
- `profile_facebook`
- `profile_twitter`

Behavior:

- When a linked profile is created, these fields can be auto-filled from another linked profile.
- When one linked profile is updated, the shared fields propagate to sibling profiles.
- When `preferred_long_name` is present on a linked profile, `users.name` is updated to match it.

## Validation Rules

Validation is implemented in:

- [`app/Http/Requests/Profile/ProfileUpsertRequest.php`](../../app/Http/Requests/Profile/ProfileUpsertRequest.php)

Key validations:

- valid email format
- enum validation for `type`
- enum validation for `honorific`
- `reg_no` format `E/YY/XXX`
- JPEG-only profile pictures
- 2 MB max upload size
- image aspect ratio validation
- URL validation for external profile links
- structured JSON validation for affiliation fields
- configurable required fields driven by `config/profiles.php`

## UI Implementation

Main views:

- [`resources/views/profile/admin/index.blade.php`](../../resources/views/profile/admin/index.blade.php)
- [`resources/views/profile/my/index.blade.php`](../../resources/views/profile/my/index.blade.php)
- [`resources/views/profile/includes/form.blade.php`](../../resources/views/profile/includes/form.blade.php)
- [`resources/views/profile/admin/delete.blade.php`](../../resources/views/profile/admin/delete.blade.php)

Livewire tables:

- [`app/Http/Livewire/Backend/ProfilesTable.php`](../../app/Http/Livewire/Backend/ProfilesTable.php)
- [`app/Http/Livewire/Backend/MyProfilesTable.php`](../../app/Http/Livewire/Backend/MyProfilesTable.php)

The index pages follow the same pattern used elsewhere in dashboard management screens such as News Management.

## Testing

Feature coverage currently includes:

- access to profile management
- self-service creation
- registration number validation
- account page profile rendering
- linked-profile synchronization
- delete confirmation flow

Relevant test file:

- [`tests/Feature/Profile/ProfileManagementTest.php`](../../tests/Feature/Profile/ProfileManagementTest.php)

Run:

```bash
php artisan test --env=testing tests/Feature/Profile/ProfileManagementTest.php
```

## Operational Notes

- If you change profile schema defaults, update the existing profile migration instead of adding ad hoc migration drift unless the change must be applied incrementally in deployed environments.
- If you change required profile fields, update `config/profiles.php` so validation and completeness stay aligned.
- If you change shared identity semantics, update both the service layer and the documentation in this file.
- If you change synchronization payload structure, update `SyncProfilesSeeder` field mapping explicitly rather than relying on passthrough arrays.
