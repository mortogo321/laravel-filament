# Laravel Filament Admin Panel

[![CI](https://github.com/mortogo321/laravel-filament/actions/workflows/ci.yml/badge.svg)](https://github.com/mortogo321/laravel-filament/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel)
![Filament](https://img.shields.io/badge/Filament-5-F59E0B)
![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)

Product-catalog admin panel showcasing **Filament 5 on Laravel 13** — full CRUD resource, dashboard widgets, global search, database notifications, dark mode.

## What's inside

- **Product resource**: full CRUD with searchable/filterable table (status, category, featured, stock, price range), bulk actions, row actions, auto-slug generation, tags, and key-value specifications
- **Dashboard widgets**: 5-metric stats overview + category-distribution chart
- **Rich forms**: sectioned layouts, rich text editor, file upload, tags input, key-value fields
- **Global search** (Cmd/Ctrl+K), database notifications, dark mode, collapsible sidebar
- **Health probes**: `GET /api/health` (JSON) + framework `GET /up`
- Seeder with 10 sample products across categories/brands for demo data

## Tech stack

- Laravel 13, Filament 5, Livewire 4
- PHP 8.3+ (runtime pinned 8.4), Bun-first frontend (Vite 8, Tailwind 4)
- SQLite for dev/test, MySQL 8.4 for prod
- PHPUnit 12, Pint, GitHub Actions CI, Dependabot

## Quickstart

```bash
git clone git@github.com:mortogo321/laravel-filament.git
cd laravel-filament

composer install
bun install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=ProductSeeder

php artisan make:filament-user

bun run build
php artisan serve
```

- Welcome page: http://localhost:8000
- Admin panel: http://localhost:8000/admin (login with the user created above)
- Health: http://localhost:8000/api/health — `{"status":"ok",...}`

## Docker

```bash
# Dev (sqlite, port 8000) — mysql service included for prod-parity experiments
docker compose -f compose.yaml up --build

# Prod (mysql 8.4, port 8080) — APP_KEY and DB_PASSWORD are required (fail-fast)
APP_KEY=<base64:key> DB_PASSWORD=<secret> docker compose -f compose.yaml -f compose.prod.yaml up --build
```

Production image is multi-stage (`vendor` → `frontend` → `production`), runs as non-root `appuser`, with `HEALTHCHECK` on `/api/health`.

## Tests

```bash
composer test        # php artisan test (sqlite :memory:)
./vendor/bin/pint --test
bun run build
```

11 tests green: API health (`/api/health`, `/up`), welcome page, admin login reachable, product factory uniqueness, JSON casts, soft deletes, unique-slug constraint, status/featured/stock filters.

## Structure

```
app/Filament/
├── Resources/Products/
│   ├── ProductResource.php
│   ├── Pages/            # List/Create/Edit/View pages
│   ├── Schemas/          # Form and infolist schemas
│   └── Tables/           # Table configuration
└── Widgets/
    ├── ProductStatsOverview.php
    └── ProductChart.php
app/Models/Product.php
routes/api.php           # GET /api/health
database/
├── factories/ProductFactory.php
└── seeders/ProductSeeder.php
tests/Feature/
├── ApiHealthTest.php
└── ProductTest.php
Dockerfile               # vendor → frontend → production (non-root + HEALTHCHECK)
compose.yaml             # dev (8000:8000, sqlite + mysql 8.4 service)
compose.prod.yaml        # prod (8080:8000, mysql 8.4, fail-fast APP_KEY/DB_PASSWORD)
.github/
├── workflows/ci.yml     # lint + test + docker
└── dependabot.yml        # weekly: composer / bun / docker / actions
```

## Reset database

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=ProductSeeder
php artisan make:filament-user
```

## Pins

| Component | Version |
|---|---|
| PHP runtime | `php:8.4-cli-bookworm` |
| Composer | binary `2.8` (`COPY --from=composer:2.8`; vendor stage is php-based because Filament 5 needs ext-intl) |
| Bun | `oven/bun:1.4.2-alpine` + `packageManager bun@1.4.2` |
| MySQL | `mysql:8.4` |
| Laravel | `13.34.0` (`^13.0`) |
| Filament | `5.9.0` (`^5.0`) |
| Livewire | `4.4.7` |
| PHPUnit | `12.5.37` (`^12.0`) |
| Pint | `1.32.1` |
| Vite | `8.3.2` |
| Tailwind | `4.3.3` |
| laravel-vite-plugin | `3.2.0` |
| axios | `1.20.0` |
| CI runner | `ubuntu-24.04` |
| GH actions | `checkout v7`, `setup-php v2 (php 8.4)`, `setup-bun v2`, `buildx v4`, `build-push v7` |

## Upgrade notes (Laravel 12 → 13, Filament 4 → 5)

- `laravel/framework ^12 → ^13`, `filament/filament ^4.1 → ^5.0`, `laravel/tinker ^2 → ^3`, `phpunit ^11 → ^12`, `php ^8.2 → ^8.3`; Livewire 3 → 4 via Filament 5.
- `config/database.php`: `PDO::MYSQL_ATTR_SSL_CA` → `Pdo\Mysql::ATTR_SSL_CA` (PHP 8.5 deprecation fix, matches Laravel 13 skeleton).
- `config/cache.php`: hyphenated default prefix + `serializable_classes => false` (deserialization hardening).
- `config/session.php`: `serialization => json` (default; overridable via `SESSION_SERIALIZATION`).
- `VerifyCsrfToken` → `PreventRequestForgery` in `AdminPanelProvider`.
- `phpunit.xml`: sqlite `:memory:` enabled for `RefreshDatabase` tests.
- Frontend: Vite 7 → 8, `laravel-vite-plugin` 2 → 3, Tailwind 4.2 → 4.3, axios/concurrently minors; yarn.lock dropped (bun-first).
- New: `routes/api.php` (`GET /api/health`), Dockerfile + compose dev/prod, CI, Dependabot, MIT LICENSE, `ProductFactory` + 9 new tests.

## License

MIT — see [LICENSE](LICENSE).
