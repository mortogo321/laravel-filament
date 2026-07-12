# Laravel Filament Admin Panel

A proof-of-concept admin panel showcasing Filament 4 on Laravel 12, built around a Product catalog resource.

## What's inside

- **Product resource**: full CRUD with searchable/filterable table (status, category, featured, stock, price range), bulk actions, row actions, auto-slug generation, tags, and key-value specifications
- **Dashboard widgets**: a 5-metric stats overview and a category-distribution chart
- **Rich forms**: sectioned layouts, rich text editor, file upload, tags input, key-value fields
- **Global search** (Cmd/Ctrl+K), database notifications, dark mode, collapsible sidebar
- Seeder with 10 sample products across categories/brands for demo data

## Tech stack

- Laravel 12, Filament 4
- PHP 8.2+
- Vite, Tailwind CSS
- SQLite (demo database)

## Quickstart

```bash
git clone git@github.com:mortogo321/laravel-filament.git
cd laravel-filament

composer install
yarn install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=ProductSeeder

php artisan make:filament-user
php artisan filament:assets

yarn build
php artisan serve
```

- Welcome page: http://localhost:8000
- Admin panel: http://localhost:8000/admin (login with the user created above)

## Structure

```
app/Filament/
├── Resources/Products/
│   ├── ProductResource.php
│   ├── Pages/            # List/Create/Edit/View pages
│   ├── Schemas/           # Form and infolist schemas
│   └── Tables/            # Table configuration
└── Widgets/
    ├── ProductStatsOverview.php
    └── ProductChart.php
app/Models/Product.php
database/seeders/ProductSeeder.php
```

## Reset database

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=ProductSeeder
php artisan make:filament-user
```
