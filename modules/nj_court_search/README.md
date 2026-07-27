# NJ Court Search — Perfex CRM Module

Standalone Perfex module that submits NJ Court searches to the external REST API and tracks status locally.

**Scope:** Perfex CRM only. Does not modify `nj-court-api`, VMware, Redis, or BullMQ.

## Audit summary (installed CRM)

| Item | Finding |
|------|---------|
| Perfex version | **2.3.5** (`migration_version` 235) |
| Module pattern | `modules/{name}/{name}.php` + `install.php` |
| Permissions | `register_staff_capabilities` + `staff_can` / `has_permission` |
| HTTP | cURL (core convention) |
| Secrets | CI `$this->encryption` + `APP_ENC_KEY` |
| Settings | `add_option` / `get_option` / `update_option` |
| Cron | `hooks()->add_action('after_cron_run', …)` |
| Controllers | `AdminController` (staff), `App_Controller` (webhook) |

## Installation

1. Ensure module folder exists: `modules/nj_court_search/`
2. Admin → **Setup → Modules** → activate **NJ Court Search**
3. Activation runs `install.php` (tables + options)
4. Confirm CSRF exclusion in `application/config/app-config.php`:

```php
$app_csrf_exclude_uris[] = 'nj_court_search/webhook';
```

5. Setup → **Staff → Roles** → assign capabilities
6. Open **NJ Court Searches → Settings** and configure API

## Activation

Activate from Setup → Modules. Deactivation keeps all history. Uninstall deletes options; tables are dropped **only** if “Delete all data when uninstalling” is enabled in settings first.

## Configuration

| Setting | Purpose |
|---------|---------|
| Enable integration | Master switch |
| API base URL | External API origin |
| API key | Encrypted; sent as `x-api-key` |
| Timeout / poll interval / batch size | HTTP + cron tuning |
| Webhook secret / tolerance / enable | HMAC callbacks |
| Cron polling fallback | Poll queued/processing jobs |
| Result retention (days) | Purge sensitive `result_json` after N days (`0` = keep forever) |
| Retention batch size | Max payloads purged per cron run |
| Purge on uninstall | Explicit data deletion flag |

**Test Connection** calls `GET {base}/health` (with fallbacks).

## Permissions

| Capability | Conceptual name | Guards |
|------------|-----------------|--------|
| `view` | nj_court_search_view | List, detail, refresh |
| `create` | nj_court_search_create | New search |
| `view_sensitive` | nj_court_search_view_sensitive | Full DOB + result JSON |
| `retry` | nj_court_search_retry | Retry action |
| `cancel` | nj_court_search_cancel | Cancel action |
| `manage_settings` | nj_court_search_manage_settings | Settings + test connection |

Admins receive all capabilities automatically (Perfex convention).

## Database tables

Prefix: `db_prefix()` (typically `tbl`)

1. `{prefix}nj_court_searches`
2. `{prefix}nj_court_search_events`
3. `{prefix}nj_court_webhook_events`

Settings use Perfex options (no settings table).

## API client

Class: `Nj_court_api_client`

- `test_connection()`, `submit_search()`, `get_search_status()`, `get_search_result()`, `retry_search()`, `cancel_search()`
- Header: `x-api-key`
- Redacted logs; normalized success/error arrays
- Retry/cancel call `/api/nj/search/{jobId}/retry|cancel` — if the external API returns 404, local fallbacks apply where safe

> If those retry/cancel routes are missing on the API: **OUTSIDE PERFEX CRM SCOPE — requires a separate project task.**

## Cron

`nj_court_search_cron` on `after_cron_run`:

1. **Result retention purge** (when retention days > 0) — independent of polling switches
2. Status polling only when integration (or mock) + cron polling enabled
   - Polls `queued` / `processing` (and reconciles `submission_failed` with job id)
   - Honors `next_poll_at`, batch size, increasing backoff
   - Does not overwrite status on API outages

## Webhook

`POST /nj_court_search/webhook`

### Signature algorithm (expected by Perfex)

1. Headers: `X-NJ-Court-Timestamp`, `X-NJ-Court-Signature`, `X-NJ-Court-Event-Id`
2. Payload string: `{timestamp}.{rawBody}`
3. Signature: `HMAC-SHA256` hex using webhook secret
4. Reject if `|now - timestamp| > tolerance`
5. Replay protection via unique `external_event_id`
6. Idempotent processing

> Implementing the matching signer in `nj-court-api` is **OUTSIDE PERFEX CRM SCOPE**.

## Manual test plan

See `docs/MANUAL_TEST_PLAN.md`, `docs/ACTIVATION.md`, `docs/UAT_MATRICES.md`.

## CSRF exclusion (required)

In `application/config/app-config.php` (not core `config.php`):

```php
$app_csrf_exclude_uris[] = 'nj_court_search/webhook';
```

This is the **smallest** exemption for Perfex 2.3.5. Do not disable CSRF globally.

## Development mock mode

Available only when `ENVIRONMENT !== 'production'`.

Settings → enable **Mock API client** and pick a scenario (`success_flow`, `no_results`, `timeout`, etc.).

Mock mode never contacts the external API, Redis, BullMQ, or VMware.

## Tests

```bash
php modules/nj_court_search/tests/webhook_signature_test.php
php modules/nj_court_search/tests/status_transition_test.php
php modules/nj_court_search/tests/retention_test.php
php modules/nj_court_search/tests/picker_auth_static_test.php
# PowerShell: lint every module PHP file
Get-ChildItem modules\nj_court_search -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

## Docs

- `docs/SCHEMA_VALIDATION.md`
- `docs/AUDIT_HARDEN.md`
- `docs/MANUAL_TEST_PLAN.md`
- `docs/ACTIVATION.md`
- `docs/UAT_MATRICES.md`
