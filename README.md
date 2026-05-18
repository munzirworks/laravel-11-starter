# Laravel 11 Starter Kit

A Laravel 11 application starter with authentication, dashboard layout, and role-based access control.

## Features

- **Laravel Breeze** (Blade) — login, registration, password reset, profile
- **Dashboard layout** — sidebar, topbar, reusable Blade components
- **Roles** — `admin` and `staff` with middleware protection
- **Admin area** — user listing (admin only)

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ (for Vite)
- MySQL, PostgreSQL, or SQLite

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Demo accounts

| Email | Password | Role |
|-------|----------|------|
| `admin@example.com` | `password` | Admin |
| `staff@example.com` | `password` | Staff |

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
