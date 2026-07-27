<?php
/**
 * Result retention purge eligibility + batch/idempotency logic (no Perfex boot).
 * Mirrors helpers in nj_court_search_helper.php and purge loop semantics.
 *
 * Run: php modules/nj_court_search/tests/retention_test.php
 */

declare(strict_types=1);

$passed = 0;
$failed = 0;

function assert_true(bool $cond, string $label): void
{
    global $passed, $failed;
    if ($cond) {
        echo "PASS  {$label}\n";
        $passed++;
    } else {
        echo "FAIL  {$label}\n";
        $failed++;
    }
}

function retention_is_enabled($days): bool
{
    if ($days === '' || $days === null || $days === false) {
        return false;
    }

    return (int) $days > 0;
}

function retention_cutoff($days, int $now): ?string
{
    if (!retention_is_enabled($days)) {
        return null;
    }

    return date('Y-m-d H:i:s', $now - ((int) $days * 86400));
}

function purge_statuses(): array
{
    return ['completed', 'no_results', 'failed', 'cancelled'];
}

function record_is_purge_eligible(array $row, string $cutoff): bool
{
    $status = $row['status'] ?? '';
    if (!in_array($status, purge_statuses(), true)) {
        return false;
    }
    if (in_array($status, ['draft', 'queued', 'processing', 'submission_failed'], true)) {
        return false;
    }
    if (!empty($row['result_purged_at'])) {
        return false;
    }
    $json = $row['result_json'] ?? null;
    if ($json === null || $json === '') {
        return false;
    }
    if (is_string($json)) {
        $decoded = json_decode($json, true);
        if (is_array($decoded) && !empty($decoded['_purged'])) {
            return false;
        }
    }
    $completedAt = $row['completed_at'] ?? null;
    if (!$completedAt) {
        return false;
    }

    return $completedAt <= $cutoff;
}

function purged_placeholder(): string
{
    return json_encode([
        '_purged' => true,
        'message' => 'Sensitive result payload removed by retention policy',
    ]);
}

/**
 * In-memory purge simulation (batch + single audit event per id).
 *
 * @return array{purged:int,rows:array,events:array}
 */
function simulate_purge(array $rows, $days, int $batch, int $now): array
{
    $cutoff = retention_cutoff($days, $now);
    if ($cutoff === null) {
        return ['purged' => 0, 'rows' => $rows, 'events' => []];
    }

    $batch = max(1, min(200, $batch));
    $events = [];
    $purged = 0;
    $placeholder = purged_placeholder();
    $nowStr = date('Y-m-d H:i:s', $now);

    // Sort by completed_at ASC like the model
    usort($rows, static function ($a, $b) {
        return strcmp($a['completed_at'] ?? '', $b['completed_at'] ?? '');
    });

    foreach ($rows as &$row) {
        if ($purged >= $batch) {
            break;
        }
        if (!record_is_purge_eligible($row, $cutoff)) {
            continue;
        }
        $row['result_json'] = $placeholder;
        $row['result_purged_at'] = $nowStr;
        $already = false;
        foreach ($events as $ev) {
            if ((int) $ev['search_id'] === (int) $row['id'] && $ev['event_type'] === 'result_retention_purged') {
                $already = true;
                break;
            }
        }
        if (!$already) {
            $events[] = [
                'search_id'  => (int) $row['id'],
                'event_type' => 'result_retention_purged',
            ];
        }
        $purged++;
    }
    unset($row);

    return ['purged' => $purged, 'rows' => $rows, 'events' => $events];
}

$now = strtotime('2026-07-17 12:00:00');
$payload = json_encode(['results' => [['docket' => 'A-1']]]);
$oldCompleted = date('Y-m-d H:i:s', $now - (40 * 86400));
$recentCompleted = date('Y-m-d H:i:s', $now - (5 * 86400));

// --- retention disabled ---
assert_true(!retention_is_enabled(0), 'retention disabled when days=0');
assert_true(!retention_is_enabled(''), 'retention disabled when days empty');
assert_true(!retention_is_enabled(null), 'retention disabled when days null');
assert_true(retention_is_enabled(30), 'retention enabled when days=30');
$disabledRun = simulate_purge([
    [
        'id' => 1,
        'status' => 'completed',
        'result_json' => $payload,
        'result_purged_at' => null,
        'completed_at' => $oldCompleted,
        'result_count' => 1,
        'result_checksum' => 'abc',
    ],
], 0, 50, $now);
assert_true($disabledRun['purged'] === 0, 'retention disabled: no records purged');

$cutoff30 = retention_cutoff(30, $now);
assert_true($cutoff30 !== null, 'cutoff exists when enabled');

// --- record not expired ---
$notExpired = [
    'id' => 2,
    'status' => 'completed',
    'result_json' => $payload,
    'result_purged_at' => null,
    'completed_at' => $recentCompleted,
    'result_count' => 1,
    'result_checksum' => 'abc',
];
assert_true(!record_is_purge_eligible($notExpired, $cutoff30), 'record not expired is not eligible');

// --- expired completed ---
$expiredCompleted = [
    'id' => 3,
    'status' => 'completed',
    'result_json' => $payload,
    'result_purged_at' => null,
    'completed_at' => $oldCompleted,
    'result_count' => 2,
    'result_checksum' => 'chk3',
];
assert_true(record_is_purge_eligible($expiredCompleted, $cutoff30), 'expired completed is eligible');

// --- expired no_results with payload ---
$expiredNoResults = [
    'id' => 4,
    'status' => 'no_results',
    'result_json' => json_encode(['results' => []]),
    'result_purged_at' => null,
    'completed_at' => $oldCompleted,
    'result_count' => 0,
    'result_checksum' => 'chk4',
];
assert_true(record_is_purge_eligible($expiredNoResults, $cutoff30), 'expired no_results with payload is eligible');

// --- active record protected ---
foreach (['draft', 'queued', 'processing', 'submission_failed'] as $active) {
    $row = [
        'id' => 10,
        'status' => $active,
        'result_json' => $payload,
        'result_purged_at' => null,
        'completed_at' => $oldCompleted,
    ];
    assert_true(!record_is_purge_eligible($row, $cutoff30), "active status protected: {$active}");
}

// --- already purged ---
$alreadyPurged = [
    'id' => 5,
    'status' => 'completed',
    'result_json' => purged_placeholder(),
    'result_purged_at' => $oldCompleted,
    'completed_at' => $oldCompleted,
];
assert_true(!record_is_purge_eligible($alreadyPurged, $cutoff30), 'already purged record not eligible');

// --- failed without payload not eligible; with payload eligible ---
$failedEmpty = [
    'id' => 6,
    'status' => 'failed',
    'result_json' => null,
    'result_purged_at' => null,
    'completed_at' => $oldCompleted,
];
assert_true(!record_is_purge_eligible($failedEmpty, $cutoff30), 'failed without payload not purged');
$failedWithPayload = [
    'id' => 7,
    'status' => 'failed',
    'result_json' => $payload,
    'result_purged_at' => null,
    'completed_at' => $oldCompleted,
];
assert_true(record_is_purge_eligible($failedWithPayload, $cutoff30), 'failed with payload is eligible');

// --- batch limit respected ---
$batchRows = [];
for ($i = 1; $i <= 5; $i++) {
    $batchRows[] = [
        'id' => 100 + $i,
        'status' => 'completed',
        'result_json' => $payload,
        'result_purged_at' => null,
        'completed_at' => date('Y-m-d H:i:s', $now - ((50 - $i) * 86400)),
        'result_count' => 1,
        'result_checksum' => 'b' . $i,
    ];
}
$batchRun = simulate_purge($batchRows, 30, 2, $now);
assert_true($batchRun['purged'] === 2, 'batch limit respected (purged=2)');
$stillEligible = 0;
foreach ($batchRun['rows'] as $r) {
    if (record_is_purge_eligible($r, $cutoff30)) {
        $stillEligible++;
    }
}
assert_true($stillEligible === 3, 'batch limit leaves remaining eligible records');

// --- first purge creates event once; repeated run idempotent ---
$single = [
    [
        'id' => 200,
        'status' => 'completed',
        'result_json' => $payload,
        'result_purged_at' => null,
        'completed_at' => $oldCompleted,
        'result_count' => 1,
        'result_checksum' => 'once',
    ],
];
$run1 = simulate_purge($single, 30, 50, $now);
assert_true($run1['purged'] === 1, 'first purge removes payload');
assert_true(count($run1['events']) === 1, 'audit event created once on first purge');
assert_true(strpos($run1['rows'][0]['result_json'], '_purged') !== false, 'result_json replaced with placeholder');
assert_true(!empty($run1['rows'][0]['result_purged_at']), 'result_purged_at stored');

$run2 = simulate_purge($run1['rows'], 30, 50, $now);
assert_true($run2['purged'] === 0, 'repeated purge execution is idempotent');
assert_true(count($run2['events']) === 0, 'no duplicate purge events on second run');

// Preserve metadata semantics (simulation does not touch these fields)
assert_true($run1['rows'][0]['status'] === 'completed', 'status preserved');
assert_true($run1['rows'][0]['result_count'] === 1, 'result_count preserved');
assert_true($run1['rows'][0]['result_checksum'] === 'once', 'checksum preserved');

echo "\nPassed: {$passed}; Failed: {$failed}\n";
exit($failed > 0 ? 1 : 0);
