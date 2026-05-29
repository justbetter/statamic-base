# Statamic Base

Foundation addon for JustBetter Statamic packages. Provides a Control Panel overview of installed `justbetter/*` and `just-better/*` Composer packages.

## Features

- **JustBetter** CP navigation section with a **Packages** overview
- Lists production and development JustBetter packages separately
- Shows installed version, latest stable Packagist version, and semver-aware update badges
- JustBetter nav and overview header use `icon_url` (and optional `icon_dark_url`) as SVG `<image href="…">` markup (browser loads assets; no server-side fetch). Dark variant toggles with Tailwind `dark:` like the rest of the CP.
- Custom permission: `view justbetter packages`

## Requirements

- PHP ^8.4
- Laravel ^12.40 or ^13.0
- Statamic ^6.0

## Installation

```bash
composer require justbetter/statamic-base
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag=justbetter-statamic-base
```

Build CP assets (required for the Inertia overview page):

```bash
cd vendor/justbetter/statamic-base
npm install
npm run build
```

During development:

```bash
npm run dev
```

## Configuration

Config file: `config/justbetter/statamic-base.php`

| Key | Default | Description |
|-----|---------|-------------|
| `packagist_cache_ttl` | `3600` | Seconds to cache Packagist responses |
| `icon_url` | `https://opensource.justbetter.nl/statamic/justbetter-logo-small-black.svg` | Light / default theme: URL for the `<image>` in the nav/header SVG |
| `icon_dark_url` | `null` | Optional. When set to a valid URL, dark mode uses this asset (same SVG, second `<image>` behind `hidden dark:block`) |

Environment variables: `STATAMIC_BASE_PACKAGIST_CACHE_TTL`, `STATAMIC_BASE_ICON_URL`, `STATAMIC_BASE_ICON_DARK_URL`

## Permissions

Assign the **View JustBetter packages** permission to roles that should access the overview. Super users always have access.

## Quality

```bash
composer quality
composer coverage
```

## License

MIT
