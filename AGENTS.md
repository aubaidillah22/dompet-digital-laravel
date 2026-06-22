# Dompet Digital — Laravel Agent Guide

## Stack

- **Backend**: Laravel 13 (PHP ^8.3)
- **Database**: SQLite (dev default in `.env.example`) / MySQL (production, used in `.env`)
- **Frontend**: Blade templates with inline CSS/JS + Vite (idle — all assets loaded from CDN or inline)
- **CDN libraries**: Chart.js, SweetAlert2, SheetJS (xlsx), jsPDF + jspdf-autotable, Font Awesome
- **Linting**: Laravel Pint (`./vendor/bin/pint`)

## Setup

```bash
# Fresh start (SQLite)
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve --port=8000

# If using MySQL, update DB_* in .env first
```

## Login credentials (after seeding)

| Role  | Username | Password |
|-------|----------|----------|
| Admin | `admin`  | `admin123` |
| User  | `andi`   | `user1234` |
| User  | `siti`   | `user1234` |
| User  | `budi`   | `user1234` |

## Architecture

- **Entry point**: Laravel routing via `routes/web.php` (all routes — web + API — are in one file)
- **Auth**: PHP sessions (not JWT). `AuthSession` middleware (`bootstrap/app.php:15`) guards protected routes.
- **Middleware alias**: `auth.session` registered in `bootstrap/app.php` — not in `Kernel` (no Kernel file exists in Laravel 13).
- **Exceptions**: JSON response auto-enabled for `api/*` routes via `bootstrap/app.php:21`.
- **Balance**: Computed on-the-fly as `SUM(income) - SUM(expense)` — not a stored column. Defined on `User` model as `$user->balance` accessor and independently in `TransactionController::getBalance()`.
- **Pagination**: `/api/transactions?page=N&limit=N&month=YYYY-MM`. Month filter optional. Same pattern for admin's `/api/admin/users/{id}/transactions`.

## Key files

| Path | Purpose |
|------|---------|
| `routes/web.php` | All routes (web page + JSON API) |
| `bootstrap/app.php` | App bootstrap, middleware alias, exception config |
| `app/Http/Controllers/AuthController.php` | Login, register, logout, session check |
| `app/Http/Controllers/DashboardController.php` | User dashboard page |
| `app/Http/Controllers/TransactionController.php` | CRUD + monthly aggregation + balance |
| `app/Http/Controllers/UserController.php` | Profile, change password |
| `app/Http/Controllers/AdminController.php` | User CRUD, stats, all transactions |
| `app/Http/Controllers/CategoryController.php` | List categories |
| `app/Http/Controllers/PageController.php` | Root `/` redirect (to dashboard or login) |
| `app/Http/Middleware/AuthSession.php` | Auth guard middleware |
| `app/Models/User.php` | User model (username, full_name, role, balance accessor) |
| `app/Models/Transaction.php` | Transaction model |
| `app/Models/Category.php` | Category model |
| `resources/views/layouts/app.blade.php` | Base layout (CDN-loaded libraries) |
| `resources/views/dashboard/index.blade.php` | User dashboard (JS-driven SPA-style, ~1366 lines) |
| `resources/views/admin/dashboard.blade.php` | Admin panel (JS-driven SPA-style, ~701 lines) |
| `database/migrations/` | Schema (users+sessions, categories, transactions) |
| `database/seeders/DatabaseSeeder.php` | Sample data (4 users, 28 transactions across 3 months) |

## API conventions

- **Base path**: `/api/<endpoint>`
- **Methods**: Routes use proper HTTP methods (`Route::get/post/put/delete`). Frontend JS sends PUT/DELETE as POST with `_method` field (Laravel handles this natively).
- **Auth**: Session cookie (`credentials: 'include'` in fetch). CSRF token in `X-CSRF-TOKEN` header (from `<meta name="csrf-token">` in layout).
- **Response**: Always JSON `{"success": true/false, ...}`. Errors use HTTP status codes.

## Frontend conventions

- All page-specific JS/CSS is embedded inline in Blade templates (no separate JS files).
- `resources/js/app.js` is empty (placeholder). `resources/css/app.css` does not exist. Vite is configured but idle.
- Dark mode default (if `localStorage` key `darkMode` is not set, body gets `dark-mode` class). Toggle via `#darkModeToggle`.
- Rupiah formatting via CSS class `rupiah-input` (auto-format on input in dashboard JS).
- Charts rendered via Chart.js inline in the dashboard view.

## Commands

```bash
# Serve
php artisan serve --port=8000

# Migrate + seed
php artisan migrate:fresh --seed

# Run tests (sqlite :memory:)
php artisan test
# or: composer run test  # runs config:clear then test

# Lint
./vendor/bin/pint

# Tinker
php artisan tinker
```

## Gotchas

- SQLite uses `strftime()` vs MySQL's `DATE_FORMAT()`. `TransactionController` handles this transparently via `DB::connection()->getDriverName()` checks.
- `resources/js/app.js` is empty; never add JS there — embed in Blade templates instead.
- `resources/css/app.css` does not exist; the file is referenced in `vite.config.js` but can be safely ignored.
- `.env.example` defaults to SQLite + database session driver; actual `.env` uses MySQL + file session driver. When switching, ensure the `sessions` table migration exists (it runs unconditionally).
- Session is stored in `session('user_id')`, `session('role')`, etc. — not in `Auth::user()`. The `User` model extends `Authenticatable` but `Auth` facade is not used.
