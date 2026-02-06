<!-- Copilot instructions for AI coding agents working on NEXA -->
# Copilot instructions — NEXA (Symfony 6.4)

Purpose: give fast, actionable context so an AI assistant can be immediately productive editing this Symfony app.

- **Quick facts:** Symfony 6.4, PHP >= 8.1, Doctrine ORM, Stimulus (assets/controllers), Twig templates.
- **Key commands:**
  - Install deps: `composer install` (auto-scripts run importmap/assets install)
  - Run migrations: `php bin/console doctrine:migrations:migrate`
  - Create DB: `php bin/console doctrine:database:create`
  - Tests: `./bin/phpunit` (Linux/macOS) or `.\bin\phpunit` (Windows)
  - Dev server: use Symfony CLI `symfony serve` or PHP built-in: `php -S 127.0.0.1:8000 -t public/`

- **Where to look first (big picture):**
  - `src/Controller/` — HTTP controllers (business logic). Example: `PackageController.php` ↔ templates in `templates/package/`.
  - `src/Entity/` — Doctrine entities and `src/Repository/` for DB access.
  - `src/Form/` — Symfony `*Type.php` form classes used by controllers (e.g., `FeatureType.php`).
  - `templates/` — Twig views. CRUD patterns use `_form.html.twig`, `_delete_form.html.twig`, `index.html.twig`, `new.html.twig`, `edit.html.twig`, `show.html.twig`.
  - `config/` — app config and routing (`config/routes.yaml`, `config/packages/`).
  - `migrations/` — Doctrine migrations (e.g., `Version20260202210440.php`).
  - `assets/` — frontend JS/CSS and Stimulus controllers (`assets/controllers/`, `controllers.json`).

- **Project conventions & patterns (concrete):**
  - Controllers map to template subfolders: controller `XController.php` → `templates/x/` (lowercase).
  - Forms are named `EntityType.php` and used in the controller's create/edit flows (see `src/Form/*Type.php`).
  - CRUD Twig partials follow a stable naming scheme (`_form`, `_delete_form`, `index`, `new`, `edit`, `show`) — reuse these when scaffolding new entities.
  - Database migrations live in `migrations/`; prefer generating migrations (`bin/console make:migration`) and then running them.

- **Front-end integration specifics:**
  - Stimulus controllers are in `assets/controllers/` and referenced by `assets/controllers.json`.
  - `public/` contains compiled/static assets (CSS under `public/css/`). Use `assets/` edits + importmap/asset install steps when adding JS.

- **Testing & developer tools:**
  - Unit/functional tests live under `tests/`. Run via the `bin/phpunit` script included in the repo root.
  - The project uses `symfony/maker-bundle` for code scaffolding — prefer using makers for consistent patterns.

- **Common edit workflow examples:**
  - To add a CRUD for a new entity `Thing`: create `src/Entity/Thing.php`, `src/Form/ThingType.php`, scaffold `src/Controller/ThingController.php`, make templates in `templates/thing/` following existing CRUD partial names, then generate and run a migration.
  - To change `PackageController.php` behavior: edit `src/Controller/PackageController.php` and update corresponding templates in `templates/package/`.

- **Integration points worth noting:**
  - Doctrine configuration and migrations (`config/packages/doctrine.yaml` + `migrations/`).
  - Composer auto-scripts perform `assets:install` and `importmap:install` on `composer install`.
  - Stimulus + Twig interactions (look for `data-controller` attributes in Twig templates).

If anything here is unclear or you want stronger rules (linting, testing thresholds, commit message format), tell me which area to expand and I will refine this file.
