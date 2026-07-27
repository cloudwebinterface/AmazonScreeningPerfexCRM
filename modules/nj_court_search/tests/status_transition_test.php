<?php
/**
 * Unit-style checks for status transition map (no Perfex boot).
 * Run: php modules/nj_court_search/tests/status_transition_test.php
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

$map = [
    'draft'             => ['queued', 'submission_failed', 'cancelled'],
    'submission_failed' => ['draft', 'queued', 'cancelled'],
    'queued'            => ['processing', 'completed', 'no_results', 'failed', 'cancelled'],
    'processing'        => ['queued', 'completed', 'no_results', 'failed', 'cancelled'],
    'completed'         => [],
    'no_results'        => [],
    'failed'            => ['draft', 'queued'],
    'cancelled'         => [],
];

function can_transition(array $map, string $from, string $to): bool
{
    if ($from === $to) {
        return true;
    }
    if (!isset($map[$from])) {
        return false;
    }

    return in_array($to, $map[$from], true);
}

assert_true(can_transition($map, 'draft', 'queued'), 'draft → queued');
assert_true(can_transition($map, 'draft', 'submission_failed'), 'draft → submission_failed');
assert_true(can_transition($map, 'submission_failed', 'queued'), 'submission_failed → queued');
assert_true(can_transition($map, 'queued', 'processing'), 'queued → processing');
assert_true(can_transition($map, 'queued', 'cancelled'), 'queued → cancelled');
assert_true(can_transition($map, 'processing', 'completed'), 'processing → completed');
assert_true(can_transition($map, 'processing', 'no_results'), 'processing → no_results');
assert_true(can_transition($map, 'processing', 'failed'), 'processing → failed');
assert_true(can_transition($map, 'failed', 'draft'), 'failed → draft (retry)');
assert_true(can_transition($map, 'completed', 'completed'), 'completed → completed (idempotent)');

assert_true(!can_transition($map, 'completed', 'processing'), 'reject completed → processing');
assert_true(!can_transition($map, 'cancelled', 'completed'), 'reject cancelled → completed');
assert_true(!can_transition($map, 'no_results', 'queued'), 'reject no_results → queued');
assert_true(!can_transition($map, 'completed', 'queued'), 'reject completed → queued');

echo "\nPassed: {$passed}; Failed: {$failed}\n";
exit($failed > 0 ? 1 : 0);
