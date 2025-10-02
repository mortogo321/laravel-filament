# Laravel Filament POC

A comprehensive proof-of-concept showcasing Filament 4.1 features with Laravel 12.

## Features

### Filament 4.1.0 Implementation
- ✅ **Dashboard Widgets** - Stats overview with 5 key metrics and category distribution chart
- ✅ **Advanced Table** - Filters, bulk actions, row actions, auto-refresh (30s)
- ✅ **Rich Forms** - Sections, grids, rich editor, file upload, tags, key-value pairs
- ✅ **Global Search** - Search products from anywhere (Cmd/Ctrl+K)
- ✅ **Database Notifications** - Real-time notifications with 30s polling
- ✅ **Dark Mode** - Full dark mode support
- ✅ **Collapsible Sidebar** - Desktop sidebar collapsible
- ✅ **Custom Styling** - Blue theme with Inter font

### Product Resource Features
- Comprehensive CRUD operations
- Image column with circular stacked display
- Searchable columns (name, category, SKU)
- Multiple filter types (status, category, featured, stock, price range)
- Bulk actions (delete, feature, hide, restore)
- Row actions (view, edit, feature, toggle visibility, duplicate)
- Auto-slug generation from product name
- Specifications with key-value pairs
- Tags management

## Tech Stack

- **Laravel** 12.32.5
- **Filament** 4.1.0
- **PHP** 8.4.13
- **Vite** 7.1.8
- **Tailwind CSS** 4.1.14
- **SQLite** (for demo purposes)

## Installation & Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & Yarn
- PHP intl extension (`brew install php-intl` on macOS)

### Step 1: Clone Repository

```bash
git clone <repository-url>
cd laravel-filament
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
yarn install
```

### Step 4: Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### Step 5: Database Setup

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed sample data (10 products)
php artisan db:seed --class=ProductSeeder
```

### Step 6: Create Admin User

```bash
php artisan make:filament-user
```

Enter your details:
- Name: Admin User
- Email: admin@admin.com
- Password: password (or your choice)

### Step 7: Publish Filament Assets

```bash
php artisan filament:assets
```

### Step 8: Build Frontend Assets

```bash
# Development build
yarn build

# Or watch for changes during development
yarn dev
```

### Step 9: Start Development Server

```bash
php artisan serve
```

Visit:
- **Welcome Page**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **Login**: Use the credentials you created in Step 6

## Development Commands

### Clear Caches

```bash
php artisan optimize:clear
```

### Rebuild Assets

```bash
yarn build
```

### Watch Assets (Development)

```bash
yarn dev
```

### Seed More Data

```bash
php artisan db:seed --class=ProductSeeder
```

### Reset Database

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=ProductSeeder
php artisan make:filament-user
```

## Project Structure

```
app/
├── Filament/
│   ├── Resources/
│   │   └── Products/
│   │       ├── ProductResource.php
│   │       ├── Pages/
│   │       │   ├── CreateProduct.php
│   │       │   ├── EditProduct.php
│   │       │   ├── ListProducts.php
│   │       │   └── ViewProduct.php
│   │       ├── Schemas/
│   │       │   ├── ProductForm.php      # Form schema
│   │       │   └── ProductInfolist.php  # View schema
│   │       └── Tables/
│   │           └── ProductsTable.php    # Table configuration
│   ├── Widgets/
│   │   ├── ProductStatsOverview.php     # Dashboard stats
│   │   └── ProductChart.php             # Category chart
│   └── Providers/
│       └── Filament/
│           └── AdminPanelProvider.php   # Panel configuration
├── Models/
│   └── Product.php
database/
├── migrations/
│   └── *_create_products_table.php
└── seeders/
    └── ProductSeeder.php
resources/
├── css/
│   ├── app.css
│   └── filament/
│       └── admin/
│           └── theme.css                # Custom Filament theme
└── views/
    └── welcome.blade.php                # Landing page
```

## Key Files

### Panel Configuration
`app/Providers/Filament/AdminPanelProvider.php`
- Theme colors and styling
- Dark mode configuration
- Navigation settings
- Middleware configuration

### Product Form
`app/Filament/Resources/Products/Schemas/ProductForm.php`
- Form sections and fields
- Live updates and auto-slug
- File uploads and rich editor

### Product Table
`app/Filament/Resources/Products/Tables/ProductsTable.php`
- Columns configuration
- Filters and bulk actions
- Auto-refresh settings

### Widgets
- `app/Filament/Widgets/ProductStatsOverview.php` - 5 stat cards
- `app/Filament/Widgets/ProductChart.php` - Doughnut chart

## Troubleshooting

### CSS Not Loading

```bash
php artisan filament:assets
php artisan optimize:clear
yarn build
```

### intl Extension Missing

```bash
# macOS with Homebrew
brew install php-intl

# Restart server after installation
```

### Port Already in Use

```bash
# Kill process on port 8000
lsof -ti:8000 | xargs kill -9

# Start server on different port
php artisan serve --port=8001
```

## Demo Credentials

After seeding:
- **Email**: admin@admin.com
- **Password**: password (or what you set during `make:filament-user`)

## Sample Products

The seeder creates 10 sample products with:
- Various categories (Electronics, Home & Garden)
- Different brands (Sony, Apple, LG, etc.)
- Stock levels and pricing
- Featured/draft statuses

## License

This is a proof-of-concept project for demonstration purposes.
