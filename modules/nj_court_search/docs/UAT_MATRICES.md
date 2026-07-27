# NJ Court Search — UAT Matrices (Perfex-only)

Generated for module hardening follow-up. **Mock mode must not contact the external API.**  
Staff notifications = Perfex `add_notification` to `submitted_by` on status change (no DOB/secrets).

## 1. Mock-mode UAT matrix

Prerequisites: non-production `ENVIRONMENT`, Settings → mock mode ON, set scenario, create/retry/cancel as noted.

| Scenario | DB record | Status | Audit events | Staff notify | List | Detail | Actions | Result count | No dup results | No dup events | Legal transitions | DOB mask | Sensitive perm | No secrets in logs | Result |
|----------|-----------|--------|--------------|--------------|------|--------|---------|--------------|----------------|---------------|-------------------|----------|----------------|--------------------|--------|
| submission success (`success_flow`) | created | queued→completed on refresh | created, attempted, status, result_received | yes on changes | badge OK | OK | refresh | sample >0 | checksum blocks dup | yes | yes | masked w/o perm | gated | redacted logger | **PASS*** |
| queued (`processing`/`success_flow` before poll) | yes | queued | yes | yes | OK | OK | cancel if allowed | 0 | n/a | yes | yes | yes | n/a | yes | **PASS*** |
| processing | yes | processing | yes | yes | OK | OK | refresh | 0 | n/a | yes | yes | yes | n/a | yes | **PASS*** |
| completed + sample results | yes | completed | result_received once | yes | OK | JSON escaped | none retry/cancel | >0 | checksum | once | terminal locked | yes | gated | yes | **PASS*** |
| no_results | yes | no_results | yes | yes | OK | empty/zero | locked | 0 | yes | yes | yes | yes | n/a | yes | **PASS*** |
| submission failure | yes | submission_failed | api_submission_failed | yes | OK | error shown | retry | 0 | n/a | yes | yes | yes | n/a | yes | **PASS*** |
| timeout | yes | submission_failed / poll error | failure event | yes | OK | error | retry if eligible | 0 | n/a | yes | yes | yes | n/a | yes | **PASS*** |
| malformed response | yes | failure path | yes | yes | OK | error | retry if eligible | 0 | n/a | yes | yes | yes | n/a | yes | **PASS*** |
| retry eligible failed | new attempt | draft→queued | retry_requested | yes | OK | OK | retry gated | prior preserved | checksum | yes | map enforced | yes | gated | yes | **PASS*** |
| cancel eligible queued | yes | cancelled | cancel_requested | yes | OK | OK | cancel gated | 0 | n/a | yes | yes | yes | n/a | yes | **PASS*** |

\*Logic verified via mock client + model transition/checksum/event code paths and automated unit/static tests. Full browser click-through on a live staff session is an admin follow-up in a non-prod instance (not executed in this agent run; no production deploy).

**Failed/cancelled retention note:** Purge includes `failed`/`cancelled` only when a sensitive `result_json` payload exists; empty payloads are skipped.

## 2. Permission UAT matrix

| Capability profile | Menu | Direct URL | List | Create form | Submit | Detail | Full DOB | Results | Sensitive audit | Refresh | Retry | Cancel | Settings | Test conn | AJAX pickers |
|--------------------|------|------------|------|-------------|--------|--------|----------|---------|-----------------|---------|-------|--------|----------|-----------|--------------|
| 1. No NJ permissions | hidden | denied | denied | denied | denied | denied | — | — | — | denied | denied | denied | denied | denied | denied |
| 2. View-only | list only | list/detail OK; create/settings denied | OK | denied | denied | OK | masked | masked | no | POST OK if view | denied | denied | denied | denied | denied |
| 3. Search creator (`view`+`create`) | list+new | create OK | OK | OK | OK | OK | masked | masked | no | OK | denied | denied | denied | denied | **allowed** |
| 4. Sensitive reviewer (+`view_sensitive`) | as granted | OK | OK | as granted | as granted | OK | **full** | **full** | once/session | OK | as granted | as granted | denied | denied | as create |
| 5. Retry/cancel supervisor (+`retry`+`cancel`) | as granted | OK | OK | as granted | as granted | OK | as granted | as granted | as granted | OK | **server-enforced** | **server-enforced** | denied | denied | as create |
| 6. Module admin (+`manage_settings`) | +settings | settings OK | OK | as granted | as granted | OK | as granted | as granted | as granted | OK | as granted | as granted | OK | OK | as create |

Server-side checks: every controller method calls `nj_court_search_staff_can(...)` / `access_denied` / `ajax_access_denied`. Buttons alone are not the control.

Static picker auth test covers AJAX create gate + POST-only refresh/retry/cancel.

## 3. Form validation UAT matrix

| Case | Validation message | Escaping | Values preserved | No dup record | CRM relationship | Idempotency key |
|------|--------------------|----------|------------------|---------------|------------------|-----------------|
| Missing first name | yes | n/a | yes | no insert | n/a | posted UUID kept on redisplay |
| Missing last name | yes | n/a | yes | no insert | n/a | kept |
| Missing DOB | yes | n/a | yes | no insert | n/a | kept |
| Malformed DOB | yes | n/a | yes | no insert | n/a | kept |
| Future DOB | yes | n/a | yes | no insert | n/a | kept |
| Valid leap-day (2000-02-29) | accepts | n/a | n/a | one record | n/a | server UUID if bad format |
| Invalid leap-day (2019-02-29) | yes | n/a | yes | no insert | n/a | kept |
| Invalid lead | yes | n/a | lead label if resolved | no | rejected | kept |
| Invalid customer | yes | n/a | yes | no | rejected | kept |
| Invalid contact | yes | n/a | yes | no | rejected | kept |
| Contact of other customer | yes | n/a | yes | no | rejected | kept |
| Apostrophes/hyphens in names | accepted | escaped on detail | n/a | one | n/a | yes |
| HTML/script in notes | stored | `html_escape`/`nl2br` on detail | yes | one | n/a | yes |
| Double-click submit | client disable + idempotency | n/a | n/a | single (key) | n/a | same key → redirect existing |
| Refresh after POST | PRG redirect to detail | n/a | n/a | single | n/a | consumed |
| Back-button resubmit | idempotency → existing | n/a | n/a | no second | n/a | same key |

Automated coverage: DOB normalize + transition rules + static controller checks. Browser PRG/back-button should be spot-checked on a staging instance.

## 4. Cron UAT (mock mode)

| Check | Expected | Result |
|-------|----------|--------|
| Integration disabled (and mock off) | poll skipped; retention still runs if days>0 | **PASS** (code path) |
| Cron fallback disabled | `poll_pending_searches` not called; retention still runs | **PASS** (code path) |
| `next_poll_at` respected | claim only due rows | **PASS** (model claim) |
| Poll batch size | `limit` from option | **PASS** (model) |
| Non-terminal only | pollable statuses | **PASS** |
| Atomic claim | update `next_poll_at` before work | **PASS** |
| Timeout | failure recorded; status not illegally flipped | **PASS** (mock timeout + transitions) |
| One failure in batch | loop continues | **PASS** (model loop) |
| Completed fetches results once | checksum / result_received | **PASS** |
| Checksum blocks dup writes | same payload skipped | **PASS** |
| Terminal stop polling | `next_poll_at` null | **PASS** |
| Retention purge separate | `purge_expired_results()` before poll gates | **PASS** + retention unit tests |

OS/aaPanel cron must already invoke Perfex cron; this module only hooks `after_cron_run`.

## 5. Result retention design summary

- Disabled when days empty/0.
- Cron batch via `nj_court_search_retention_batch_size` (1–200).
- Terminal statuses only; only rows with non-empty non-placeholder `result_json`.
- Clears `result_json` to `_purged` placeholder; sets `result_purged_at`.
- Keeps search row, audit trail, IDs, names, status, counts, checksums, timestamps.
- Event `result_retention_purged` once per search; re-runs are no-ops.
