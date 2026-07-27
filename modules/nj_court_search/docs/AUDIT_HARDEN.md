# Audit & harden report — nj_court_search (Perfex 2.3.5)

Date: 2026-07-15  
Scope: `modules/nj_court_search` + required CSRF exclude in `app-config.php`.

PHP baseline for this install: **8.1+** (`APP_MINIMUM_REQUIRED_PHP_VERSION`). PHP 7 concerns from the brief are informational for this fork.

---

## 1. Findings by severity

### Critical (fixed)

| ID | Finding | Fix |
|----|---------|-----|
| C1 | `_event_data` merged into SQL `UPDATE` columns (invalid column / write failure) | Strip `_event_data` before `update_search` |
| C2 | No status transition enforcement (e.g. completed→processing) | Allowed-transition map + reject illegal moves |

### High (fixed)

| ID | Finding | Fix |
|----|---------|-----|
| H1 | Contact not required to belong to selected customer | `validate_links` checks `contacts.userid` |
| H2 | Validation failure redirected without preserving form values | Re-render form with `$form` |
| H3 | `test_connection` required API key though `/health` is public | Optional auth on health request |
| H4 | Webhook `hash_equals` with length mismatches could throw (PHP 8) | `nj_court_search_signatures_match` length-safe compare |
| H5 | Cron soft-lock non-atomic (duplicate poll risk) | Conditional claim on `next_poll_at` |

### Medium (fixed)

| ID | Finding | Fix |
|----|---------|-----|
| M1 | Duplicate `result_received` events | Skip event when checksum unchanged |
| M2 | Sensitive-view audit on every page refresh | Once per session per search |
| M3 | Undecryptable secrets returned as plaintext fallback | Return empty string |
| M4 | API response keys inconsistent (`http_code` vs `status_code`) | Normalized shape + legacy alias |
| M5 | `json_encode` request body unchecked | Fail with `json_encode_error` |
| M6 | No safe mock mode for Perfex-only testing | Dev-only mock client + settings |

### Low / Informational

| ID | Finding | Disposition |
|----|---------|-------------|
| L1 | Retention days setting has no purge job | Perfex-only TODO |
| L2 | Lead/contact pickers basic | TODO |
| I1 | Project requires PHP 8.1 — typed tests OK | Informational |
| I2 | CSRF exclude already minimal for webhook only | Confirmed |
| I3 | External retry/cancel endpoints may 404 | **OUTSIDE PERFEX CRM SCOPE** |

---

## 2. Files changed

- `helpers/nj_court_search_helper.php` — transitions, mock gates, signature helper, secret hardening
- `models/Nj_court_search_model.php` — transition enforcement, cron claim, validate_links, result idempotency, retry reset
- `libraries/Nj_court_api_client.php` — normalized responses, mock mode, health without key
- `controllers/Nj_court_search.php` — form preserve, mock settings, sensitive audit throttle
- `controllers/Webhook.php` — safe signature compare, insert failure handling
- `views/form.php` — retained values
- `views/settings.php` — mock UI (non-production only)
- `install.php` / `uninstall.php` — mock options
- `language/english/nj_court_search_lang.php` — mock strings
- `tests/webhook_signature_test.php` — new
- `tests/status_transition_test.php` — new
- `docs/SCHEMA_VALIDATION.md` — new
- `docs/AUDIT_HARDEN.md` — this file
- `README.md` — updated CSRF + mock notes

No changes to `nj-court-api`, Redis, BullMQ, VMware, or aaPanel.

---

## 3. Permission matrix

| Action | Capability |
|--------|------------|
| Menu / list / DataTables AJAX | `view` |
| Detail page | `view` |
| Full DOB + result JSON | `view_sensitive` |
| New search form + submit | `create` |
| Manual refresh (POST) | `view` |
| Retry (POST) | `retry` |
| Cancel (POST) | `cancel` |
| Settings + save | `manage_settings` |
| Test connection (POST AJAX) | `manage_settings` |
| Webhook | HMAC only (no staff session) |

Admins receive all capabilities (Perfex `staff_can`).

---

## 4. Status transition matrix

| From \ To | draft | submission_failed | queued | processing | completed | no_results | failed | cancelled |
|-----------|-------|-------------------|--------|------------|-----------|------------|--------|-----------|
| draft | = | ✓ | ✓ | | | | | ✓ |
| submission_failed | ✓ | = | ✓ | | | | | ✓ |
| queued | | | = | ✓ | ✓ | ✓ | ✓ | ✓ |
| processing | | | ✓ | = | ✓ | ✓ | ✓ | ✓ |
| completed | | | | | = | | | |
| no_results | | | | | | = | | |
| failed | ✓ (retry) | | ✓ | | | | = | |
| cancelled | | | | | | | | = |

`=` = idempotent same-status allowed.

---

## 5–11. Reviews

See also `SCHEMA_VALIDATION.md`, module `README.md`.

**Cron:** enabled flags required; polls only `queued`/`processing` with job id; `next_poll_at` claim; batch size; backoff; one failure does not abort batch; no DOB/secrets in logs.

**Webhook:** POST only; `{timestamp}.{rawBody}` HMAC-SHA256; length-safe `hash_equals`; tolerance; unique `external_event_id`; unknown job → 202; transition rules enforced; secret never logged.

**Sensitive data:** DOB masked without `view_sensitive`; secrets encrypted; settings show mask only; results escaped in `<pre>`; sensitive view audited once/session.

**UI:** Perfex `init_head`/`init_tail`, panel layout, badges, DataTables, permission-aware buttons.

**CSRF:** only `$app_csrf_exclude_uris[] = 'nj_court_search/webhook';` — smallest exemption.

---

## 12–13. Tests (executed)

See parent response for command output.

---

## 14. Remaining Perfex-only TODOs

- Optional dedicated PHPUnit suite inside CI
- Role-restriction notes remain informational (enforce via Perfex role capabilities)
- Live browser mock-mode click-through on a shared non-prod Perfex instance (logic covered; UI spot-check remaining)
- Optional: richer notification preference / email templates for status changes

## 15. Outside Perfex CRM scope

- Implementing API retry/cancel routes in `nj-court-api`
- Emitting signed webhooks from the API worker
- Redis/BullMQ/VMware/Windows automation
- Production deploy / aaPanel
