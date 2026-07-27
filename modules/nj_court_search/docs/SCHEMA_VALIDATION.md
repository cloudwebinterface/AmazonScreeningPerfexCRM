# Database schema validation — nj_court_search

Prefix: dynamic via `db_prefix()` (default `tbl`). Engine: InnoDB. Charset: `$CI->db->char_set`.

Activation is idempotent (`table_exists` guards). Deactivation never drops data. Uninstall drops tables only when `nj_court_search_purge_on_uninstall=1`.

## `{prefix}nj_court_searches`

| Column | Type | Nullable | Default | Index | Purpose |
|--------|------|----------|---------|-------|---------|
| id | INT(11) AI PK | NO | — | PRIMARY | Local search id |
| external_job_id | VARCHAR(64) | YES | NULL | idx_nj_court_external_job_id | Remote BullMQ/API job UUID |
| idempotency_key | CHAR(36) | NO | — | UNIQUE uk_nj_court_idempotency | Double-submit / idempotent create |
| first_name | VARCHAR(100) | NO | — | | Subject first name |
| middle_name | VARCHAR(100) | YES | NULL | | Optional middle |
| last_name | VARCHAR(100) | NO | — | | Subject last name |
| suffix | VARCHAR(50) | YES | NULL | | Optional suffix |
| dob | DATE | NO | — | | Date of birth (never logged in full) |
| reference_id | VARCHAR(100) | YES | NULL | idx_nj_court_reference_id | CRM / external reference |
| lead_id | INT(11) | YES | NULL | idx_nj_court_lead_id | Linked Perfex lead |
| client_id | INT(11) | YES | NULL | idx_nj_court_client_id | Linked customer |
| contact_id | INT(11) | YES | NULL | idx_nj_court_contact_id | Linked contact |
| notes | TEXT | YES | NULL | | Staff notes |
| status | VARCHAR(32) | NO | draft | idx_nj_court_status | Local state machine |
| result_count | INT(11) | NO | 0 | | Number of cases |
| external_result_version | VARCHAR(64) | YES | NULL | | Optional remote version |
| result_checksum | VARCHAR(64) | YES | NULL | | SHA-256 of result JSON |
| result_json | LONGTEXT | YES | NULL | | Normalized result payload (redacted to purge placeholder after retention) |
| result_purged_at | DATETIME | YES | NULL | | When sensitive result payload was purged |
| error_code | VARCHAR(64) | YES | NULL | | Sanitized error code |
| error_message | TEXT | YES | NULL | | Sanitized error message |
| submitted_by | INT(11) | YES | NULL | | Staff id |
| submitted_at | DATETIME | YES | NULL | | First successful API submit |
| processing_started_at | DATETIME | YES | NULL | | First processing transition |
| completed_at | DATETIME | YES | NULL | | Terminal timestamp |
| last_checked_at | DATETIME | YES | NULL | | Last status poll |
| next_poll_at | DATETIME | YES | NULL | idx_nj_court_next_poll_at | Cron claim/schedule |
| retry_count | INT(11) | NO | 0 | | Retry attempts |
| created_at | DATETIME | NO | — | idx_nj_court_created_at | Created |
| updated_at | DATETIME | NO | — | | Updated |

Index name lengths: all under MySQL 64-char limit.

## `{prefix}nj_court_search_events`

| Column | Type | Nullable | Default | Index | Purpose |
|--------|------|----------|---------|-------|---------|
| id | INT AI PK | NO | | PRIMARY | Event id |
| search_id | INT | YES | NULL | idx_nj_court_events_search_id | Related search (null for settings/webhook reject) |
| event_type | VARCHAR(64) | NO | | idx_nj_court_events_type | Audit type |
| old_status | VARCHAR(32) | YES | NULL | | Prior status |
| new_status | VARCHAR(32) | YES | NULL | | New status |
| event_data | TEXT | YES | NULL | | Non-secret JSON metadata |
| staff_id | INT | YES | NULL | | Acting staff |
| external_event_id | VARCHAR(128) | YES | NULL | idx_nj_court_events_external | Webhook event link |
| created_at | DATETIME | NO | | idx_nj_court_events_created | Created |

## `{prefix}nj_court_webhook_events`

| Column | Type | Nullable | Default | Index | Purpose |
|--------|------|----------|---------|-------|---------|
| id | INT AI PK | NO | | PRIMARY | Row id |
| external_event_id | VARCHAR(128) | NO | | UNIQUE uk_nj_court_webhook_event | Replay protection |
| external_job_id | VARCHAR(64) | YES | NULL | idx_nj_court_webhook_job | Matched job |
| payload_hash | VARCHAR(64) | NO | | | SHA-256 of raw body |
| signature_timestamp | VARCHAR(32) | YES | NULL | | Header timestamp |
| processed | TINYINT(1) | NO | 0 | idx_nj_court_webhook_processed | Processed flag |
| processing_error | TEXT | YES | NULL | | Error note |
| received_at | DATETIME | NO | | idx_nj_court_webhook_received | Received |
| processed_at | DATETIME | YES | NULL | | Processed |

## Options (not a table)

Secrets `nj_court_search_api_key` and `nj_court_search_webhook_secret` are stored encrypted via CI Encryption (`APP_ENC_KEY`). Settings page never redisplays plaintext.
