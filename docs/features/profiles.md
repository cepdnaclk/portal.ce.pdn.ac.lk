# User Profiles

## Overview

User Profiles store person data (students, academic staff, external people) independently of Portal accounts. One email = one profile; a profile can hold multiple profile types with type-specific attributes. Profiles are auto-linked to accounts on user creation and first login, and can be seeded from the department People API with `profiles:sync`.

## Data model

`App\Domains\Profile\Models\UserProfile` stores:

- `email` (unique, primary identity) and `alternate_email` (students often have faculty + personal email)
- Name fields: `honorific`, `full_name`, `name_with_initials`, `preferred_short_name`, `preferred_long_name`
- `location`, `current_affiliation`, `current_position`, `profile_image` (URL)
- `user_id` — the linked Portal account, nullable, set null on user deletion
- Soft deletes; changes are activity-logged

Related models:

- `UserProfileType` — one row per assigned type (`STUDENT`, `ACADEMIC_STAFF`, `EXTERNAL`), unique per profile. Type-specific data lives in the `attributes` JSON column (students: `reg_number`, `batch`, `department`, `interests`; staff: `designation`, `research_interests`; external: `affiliation`, `position`, `interests`). `source_key` holds the People API identity (eNumber / username) and makes syncs idempotent — unique per `(type, source_key)`.
- `UserProfileLink` — one URL per link type (`linkedin`, `github`, `cv`, `website`, `google_scholar`, `researchgate`, `facebook`, `twitter`, `other`), unique per profile.

### Completeness

`$profile->completeness` returns a percentage over a fixed checklist: the 5 name fields, location, affiliation, position, image, having at least one link, plus the required attributes of each assigned type. `isComplete()` means ≥ 50%.

## Permissions

Seeded by `Database\Seeders\Roles\ProfileRoleSeeder`:

- `user.access.profiles` (parent, granted to Administrator)
  - `user.access.profiles.editor` — create/edit/delete any profile
  - `user.access.profiles.viewer` — read-only admin view

A "Profile Manager" role holds all three. Editing your own profile needs no permission — only an authenticated account whose email is linked to the profile.

## Admin UI

`/dashboard/profiles` (routes in `routes/backend/profiles.php`, guarded by editor|viewer): a Livewire table with search, type filter, and completeness column, plus create/edit/show/delete screens. All mutations go through `App\Domains\Profile\Services\ProfileService`. The dashboard shows profile counts by type for permitted users.

## Self-service

Authenticated users manage their own profile at `/intranet/profile/manage` (linked from My Account). The profile is always resolved from the logged-in user — a missing profile is created on first visit. Users can edit names, location/affiliation, links, and the attributes of their assigned types; they cannot change their email, add/remove types, or (for students) change `reg_number`/`batch`. These limits are enforced server-side in `UpdateMyProfileRequest`.

## Onboarding

Backend pages show a warning banner ("Your profile is X% complete") while completeness is below 50%, linking to a three-step wizard at `/dashboard/profile/onboarding` (names → location & affiliation → links). The banner disappears automatically at ≥ 50% — no dismissed flag is stored.

## Auto-linking

`UserEventListener` calls `ProfileService::findOrCreateForUser` on `UserCreated` and on first login: an existing unlinked profile matching the user's email (or alternate email) is linked, otherwise a minimal profile is created. Failures are logged and never break user creation or login.

## Syncing from the People API

```bash
# Preview: full pass in a transaction, prints counts, rolls back
php artisan profiles:sync --dry-run

# Import/refresh both feeds (or limit with --students / --staff)
php artisan profiles:sync
```

The sync is idempotent and safe on cron. Identity is resolved by `(type, source_key)` first, then by email (adopting profiles created by auto-linking), then a new profile is created. Non-empty API values overwrite profile fields; empty values never clobber existing data; links are upserted but never deleted; student records with no constructible email are skipped and counted (`skipped_no_email`). A person in both feeds resolves to one profile with two types.

Once student self-service replaces the API as the source of truth, switch students to seed-only (see the note in `profiles:sync --help`).
