# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Setup

```bash
composer setup   # installs deps, copies .env, generates app key, runs migrations, builds assets
```

## Development

```bash
composer dev     # starts artisan serve + queue:listen + vite concurrently
```

## Testing

```bash
composer test                            # clears config cache, then runs all tests
php artisan test --filter ExampleTest    # run a single test class
php artisan test tests/Feature/ExampleTest.php  # run a specific file
```

Tests use [Pest](https://pestphp.com/) — write tests in Pest syntax, not raw PHPUnit.

## Code Style

```bash
./vendor/bin/pint    # Laravel Pint (opinionated PSR-12 formatter)
```

## Stack

- **PHP 8.3 / Laravel 13** — backend framework
- **Pest 4** — test runner (with `pest-plugin-laravel`)
- **Vite 8 + Tailwind CSS v4** — frontend build pipeline; entry points are `resources/css/app.css` and `resources/js/app.js`
- **Bunny Fonts** — "Instrument Sans" loaded via `laravel-vite-plugin/fonts` in `vite.config.js`
- **SQLite** — default dev database (file at `database/database.sqlite`)

## Architecture

This is a standard Laravel MVC application. Key conventions to follow:

- **Routes** live in `routes/web.php` (HTTP) and `routes/console.php` (Artisan commands).
- **Controllers** extend `App\Http\Controllers\Controller` (thin base class).
- **Models** live in `app/Models/`; the `User` model is the only one scaffolded.
- **Blade views** live in `resources/views/`; Tailwind v4 is imported directly via `@import 'tailwindcss'` in `app.css` (no `tailwind.config.js` — configuration is done via CSS `@theme` blocks).
- **Migrations** follow the standard `database/migrations/` convention; seeders are in `database/seeders/`.
- Queue jobs should be dispatched normally; `composer dev` starts a queue worker automatically.