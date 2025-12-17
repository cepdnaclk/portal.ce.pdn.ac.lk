# Repository Guidelines

## Project Structure & Module Organization

-   `app/` contains domain services, HTTP controllers, and console commands; place new logic beside its domain peers to reuse policies.
-   UI assets live in `resources/js` (Vue 2) and `resources/sass`; Blade layouts in `resources/views` compile to `public/` via Laravel Mix.
-   Register routes under `routes/frontend`, `routes/backend`, `routes/intranet`, or the shared `web.php`/`api.php`; store database artifacts in `database/` and ops helpers in `scripts/`.

## Build, Test, and Development Commands

-   `composer install` and `pnpm install` bootstrap PHP and node dependencies; favor pnpm to respect the checked-in lockfile.
-   `pnpm run dev` builds assets once, while `pnpm run watch` auto-recompiles during Vue or Sass edits.
-   `php artisan serve --host=0.0.0.0 --port=8000` exposes the app locally; reset data with `php artisan migrate:fresh --seed` when features depend on seeded roles.

## Coding Style & Naming Conventions

-   Run `composer format` (php-cs-fixer) before committing; adhere to PSR-12 spacing, 4-space indentation, and PascalCase class names under `App\`.
-   JavaScript follows `.prettierrc.json` (tabWidth 4, single quotes, no semicolons, trailingComma `es5`); store Vue components using kebab-case filenames.
-   Name controllers, policies, and route aliases after their feature area (e.g., `TaxonomyPageController`, routes `taxonomy.pages.*`) to stay aligned with the REST matrix in `README.md`.

## Testing Guidelines

-   `composer test` runs the parallel PHPUnit suite; prefer `php artisan test` for focused or serialized database runs.
-   `composer test-coverage` produces HTML results in `coverage/`; share highlights when modifying `app/Domains` or security-sensitive middleware.
-   Configure `.env.testing` with an isolated database, run `php artisan migrate --env=testing`, and keep test classes within `tests/Feature` or `tests/Unit` with a `Test` suffix.

## Commit & Pull Request Guidelines

-   Write imperative messages (`Add taxonomy audit logging`) and append issue or PR references as `(#123)` to mirror the existing history style.
-   Prefix urgent production fixes with `hotfix:` or `[Hotfix]`, and title release branches as `Release x.y.z` to match tagging.
-   PRs should describe user impact, call out required seeders or scripts, include UI screenshots when relevant, and wait for Laravel CI plus Codecov.

## Security & Configuration Tips

-   Copy `.env.example` to `.env`, keep secrets out of Git, and refresh keys with `php artisan key:generate` during new setups.
-   Run `php artisan storage:link` after cloning and execute deployment helpers from `scripts/` (with `sudo` when noted) to keep automation reliable.
