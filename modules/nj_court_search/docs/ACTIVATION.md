# NJ Court Search — Activation & Reactivation (Perfex CRM 2.3.5)

**Scope:** Perfex module only. Do not activate or deploy on production from this checklist without a separate change window.

## Prerequisites

1. Perfex CRM 2.3.5 with staff admin access.
2. Module files present under `modules/nj_court_search/`.
3. CSRF exclusion already present in `application/config/app-config.php`:
   - `$app_csrf_exclude_uris[] = 'nj_court_search/webhook';`
4. PHP encryption key configured (Perfex `encryption_key`) so API key / webhook secret can be stored encrypted.

## First-time activation

1. Log in as administrator.
2. Go to **Setup → Modules**.
3. Find **NJ Court Search** and click **Activate**.
4. Confirm activation completes without PHP errors.
5. Go to **Setup → Roles** (or staff permissions) and assign capabilities under **NJ Court Searches**:
   - `view`, `create`, `view_sensitive`, `retry`, `cancel`, `manage_settings` as needed.
6. Open **NJ Court Searches → NJ Court Search Settings**.
7. Configure:
   - Enable integration (or enable **mock mode** on non-production only).
   - API base URL / API key (leave blank in mock UAT).
   - Webhook secret if using webhooks.
   - Result retention days (`0` = keep forever) and retention batch size.
   - Leave **Purge on uninstall** unchecked unless you intend to destroy data on uninstall.
8. Confirm left navigation shows **NJ Court Searches** only for staff with `view`.

## Reactivation (after deactivate)

1. Deactivation does **not** delete tables, searches, events, or options.
2. Re-activate via **Setup → Modules → Activate**.
3. Expected idempotent behavior:
   - Missing tables are created.
   - Existing tables and search rows are preserved.
   - Missing column `result_purged_at` is added if absent.
   - Missing options are added via `add_option` (no-op when present).
   - Existing encrypted `nj_court_search_api_key` and `nj_court_search_webhook_secret` remain unchanged.
   - `mock_mode` / `mock_scenario` options are added if missing; existing values preserved.
   - Permissions re-register on `admin_init`.
   - Language files load via `register_language_files`.

## Deactivation vs uninstall

| Action | Tables / search data | Options / secrets | Permissions |
|--------|----------------------|-------------------|-------------|
| Deactivate | Preserved | Preserved | Feature inactive until reactivation |
| Uninstall (purge off) | Preserved | Removed | Removed with module |
| Uninstall (purge on) | Dropped | Removed | Removed with module |

**Purge on uninstall** must be explicitly enabled in module settings before uninstall to drop tables.

## Verification after activate/reactivate

- [ ] Tables exist: `{prefix}nj_court_searches`, `{prefix}nj_court_search_events`, `{prefix}nj_court_webhook_events`
- [ ] Column `result_purged_at` exists on searches table
- [ ] Options include retention batch size and mock options
- [ ] Menu visible only with `view`
- [ ] Settings page only with `manage_settings`
- [ ] Create form searchable pickers load for staff with `create`

## Do not

- Do not enable mock mode when `ENVIRONMENT=production` (blocked in code).
- Do not point production at unfinished external API without a separate ops task.
- Do not set purge-on-uninstall unless data destruction is intentional.
