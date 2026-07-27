# Manual test plan — nj_court_search

Do not mark items passed unless actually executed against a running Perfex instance.

| # | Test | Steps | Expected |
|---|------|-------|----------|
| 1 | Module activation | Setup → Modules → Activate | Tables + options created; no PHP errors |
| 2 | DB migration | Inspect DB | `tblnj_court_searches`, `_events`, `_webhook_events` exist with indexes |
| 3 | Permission enforcement | Role without `view` | Menu hidden; URLs → access denied |
| 4 | Menu visibility | Role with `view` only | Sees list; no Settings; no New if no `create` |
| 5 | New search validation | Submit empty / bad DOB / future DOB | Server + client errors; no API call |
| 6 | Successful API submit | Mock/stub API returning jobId | Status `queued`; detail page; audit events |
| 7 | Failed API submit | Stop API / bad key | Status `submission_failed`; not shown as queued |
| 8 | Duplicate submission | Re-post same idempotency key | Redirect to existing; warning |
| 9 | List filters | Status + date range + search | DataTables filters correctly |
| 10 | DOB masking | User without `view_sensitive` | DOB masked `**/**/YYYY` |
| 11 | Detail permissions | Without sensitive | Results masked alert |
| 12 | Manual refresh | POST refresh on queued job | Status updated; audit `manual_refresh` |
| 13 | Cron status update | Run admin cron with pending rows | `next_poll_at` respected; status advances |
| 14 | Completed results | API completed + result payload | `completed` / `no_results`; JSON stored |
| 15 | Retry rules | Retry completed | Blocked; retry failed → allowed |
| 16 | Cancel rules | Cancel completed | Blocked; cancel queued → allowed |
| 17 | Valid webhook | Correct HMAC + event id | 200; status applied |
| 18 | Invalid signature | Wrong HMAC | 401; `webhook_rejected` |
| 19 | Stale timestamp | Old timestamp | 401 |
| 20 | Replayed event | Same Event-Id twice | 200 duplicate; no double transition |
| 21 | Deactivation | Deactivate module | Data retained |
| 22 | Uninstall preserve | Uninstall with purge off | Tables remain; options removed |
| 23 | Uninstall purge | Enable purge, uninstall | Tables dropped |
