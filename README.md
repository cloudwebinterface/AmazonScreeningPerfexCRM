# AmazonScreening (Staging)

Legacy admin portal for managing Amazon background screening searches. Built on **Perfex CRM 2.3.x** and **CodeIgniter 3.1.10**, extended with custom workflows and an **Accurate Background** vendor API integration.

This repository is the **staging / legacy** instance (`amazonscreening-stg`). It is not a greenfield app—the codebase inherits Perfex CRM modules (invoices, clients, staff, etc.) while the primary day-to-day work happens in the **Searches** area.

## Features

- **Search management** — pending searches, employee searches, case submission, notes, PDF export
- **Accurate Background API** — fetch, sync, and reload search data from the vendor API
- **Webhooks** — receive new search notifications and import them into the local database
- **Reports & summary** — reporting dashboards and import utilities
- **Staff admin** — authentication, roles, and Perfex CRM admin tooling

## Tech stack

| Layer | Technology |
|-------|------------|
| Language | PHP 8.1+ (tested on 8.4 / 8.5) |
| Framework | CodeIgniter 3.1.10 |
| CRM base | Perfex CRM 2.3.2 |
| Database | MySQL / MariaDB |
| Dependencies | Composer (`application/vendor/`) |
| Frontend assets | Grunt (optional, for rebuilding minified CSS/JS) |

## Requirements

- PHP **8.1.0** or higher with extensions: `mbstring`, `mysqli`, `curl`, `gd`, `zip`
- MySQL 5.7+ or MariaDB 10.3+
- Web server with URL rewriting (Apache `mod_rewrite` or nginx equivalent)
- Composer (only if reinstalling `vendor/`)

## Quick start (local)

### 1. Clone and configure

```bash
git clone <repository-url> amazonscreening-stg
cd amazonscreening-stg

cp application/config/app-config-sample.php application/config/app-config.php
```

Edit `application/config/app-config.php`:

| Constant | Description |
|----------|-------------|
| `APP_BASE_URL` | Public site URL with trailing slash |
| `APP_ENC_KEY` | 32-character encryption key (do not change after go-live) |
| `APP_DB_*` | Database hostname, user, password, database name |
| `AB_API_USER` / `AB_API_PASS` / `AB_API_HOST` | Accurate Background API credentials |
| `AB_DATA` | `api` (production) or `testapi` (development) |

> `app-config.php` is gitignored. Never commit credentials.

### 2. Database

Import an existing database dump into MySQL, or use a copy of the staging database. The app expects Perfex CRM tables plus custom search-related tables managed by `searches_model`.

### 3. Web server

Point the document root to this directory. Example with [Laravel Valet](https://valetdocs.net):

```bash
valet link old.amazonscreening
# https://old.amazonscreening.test
```

Ensure `uploads/`, `application/cache/`, and `application/logs/` are writable by the web server.

### 4. Log in

Admin URL: `/admin/authentication`

Default landing page after login: `/admin` → **Searches**

## Project structure

```
amazonscreening-stg/
├── application/          # App code (controllers, models, views, config)
│   ├── config/         # CI + app configuration
│   ├── controllers/    # HTTP entry points (admin/, Api.php, Webhook.php, …)
│   ├── helpers/        # api_helper.php, general_helper.php, …
│   ├── models/         # Data layer (searches_model, …)
│   └── vendor/         # Composer dependencies
├── assets/             # CSS, JS, images, plugins
├── modules/            # Perfex CRM modules (backup, goals, surveys, …)
├── system/             # CodeIgniter core (includes PHP 8.4+ patches)
├── uploads/            # User-uploaded files (gitignored except placeholders)
├── index.php           # Front controller
└── pipe.php            # Email piping endpoint (Perfex)
```

## Key routes

| URL | Purpose |
|-----|---------|
| `/admin` | Searches dashboard (default) |
| `/admin/authentication` | Staff login |
| `/admin/search/{id}` | Search detail |
| `/admin/new-requests` | New request queue |
| `/admin/summary` | Summary view |
| `/api/autofetchdata` | Pull searches from Accurate Background API |
| `/webhook/new` | Webhook endpoint for new search IDs |
| `/cron` | Scheduled tasks (Perfex cron; protect with `APP_CRON_KEY`) |

## Cron

Perfex CRM relies on a periodic cron job. Configure your scheduler to hit:

```
https://your-domain.test/cron/{APP_CRON_KEY}
```

Or run via CLI if supported by your hosting setup.

## Development

### Composer

Dependencies are committed under `application/vendor/`. To refresh:

```bash
cd application
composer install
```

### Frontend assets

To rebuild minified admin assets (optional):

```bash
npm install
npx grunt
```

### Logs

Application logs are written to `application/logs/` and are excluded from git. Check there first when debugging API sync or login issues.

## Git hygiene

The root `.gitignore` excludes:

- `application/config/app-config.php` (secrets)
- `application/logs/`, runtime cache, and user uploads
- `pma/` (local phpMyAdmin — do not deploy)
- Archives (`*.zip`, `*.sql`, …)

Use `application/config/app-config-sample.php` as the configuration template for new environments.

## PHP 8.4+ notes

This fork includes compatibility patches for modern PHP in:

- `system/` — CodeIgniter session handling, dynamic properties, deprecations
- `application/` — PHPMailer 6, Illuminate 11, `(bool)` cast updates, mailer fixes

Minimum PHP version is enforced in `application/config/config.php` (`APP_MINIMUM_REQUIRED_PHP_VERSION`).

## License

Based on **Perfex CRM** (commercial license) and **CodeIgniter** (MIT). See `license.txt` for Perfex CRM license terms. Custom AmazonScreening code is proprietary to the project owner.
