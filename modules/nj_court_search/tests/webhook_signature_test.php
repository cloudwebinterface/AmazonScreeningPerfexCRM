<?php
/**
 * Standalone webhook signature verification vectors for nj_court_search.
 * Does not boot Perfex. Run: php modules/nj_court_search/tests/webhook_signature_test.php
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

function compute_sig(string $timestamp, string $rawBody, string $secret): string
{
    return hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);
}

function signatures_match(string $expectedHex, string $provided): bool
{
    $expected = strtolower(trim($expectedHex));
    $provided = strtolower(trim($provided));
    if ($expected === '' || $provided === '' || strlen($expected) !== strlen($provided)) {
        return false;
    }

    return hash_equals($expected, $provided);
}

function is_stale(string $timestamp, int $tolerance, int $now): bool
{
    if (!ctype_digit($timestamp)) {
        return true;
    }

    return abs($now - (int) $timestamp) > $tolerance;
}

$secret = 'test-webhook-secret-value';
$now = 1_700_000_000;
$tolerance = 300;
$body = '{"jobId":"abc-123","status":"completed","event":"search.completed"}';
$ts = (string) $now;
$validSig = compute_sig($ts, $body, $secret);

// 1. Valid signature
assert_true(signatures_match($validSig, $validSig), 'valid signature matches');

// 2. Case-insensitive hex
assert_true(signatures_match($validSig, strtoupper($validSig)), 'uppercase signature matches');

// 3. Invalid signature
assert_true(!signatures_match($validSig, hash_hmac('sha256', 'x', $secret)), 'invalid signature rejected');

// 4. Missing signature
assert_true(!signatures_match($validSig, ''), 'missing signature rejected');

// 5. Length mismatch does not throw
assert_true(!signatures_match($validSig, 'abcd'), 'short signature rejected safely');

// 6. Stale timestamp
assert_true(is_stale((string) ($now - 301), $tolerance, $now), 'stale timestamp detected');

// 7. Fresh timestamp
assert_true(!is_stale((string) ($now - 10), $tolerance, $now), 'fresh timestamp accepted');

// 8. Non-numeric timestamp
assert_true(is_stale('not-a-time', $tolerance, $now), 'non-numeric timestamp rejected');

// 9. Wrong body ⇒ different signature
$other = compute_sig($ts, $body . ' ', $secret);
assert_true(!signatures_match($validSig, $other), 'body tampering changes signature');

// 10. Replay event IDs simulated as unique set
$seen = [];
$eventId = 'evt-001';
$first = !isset($seen[$eventId]);
$seen[$eventId] = true;
$second = !isset($seen[$eventId]);
assert_true($first && !$second, 'replayed event id detected as duplicate');

// 11. Malformed JSON
$decoded = json_decode('{bad', true);
assert_true(!is_array($decoded), 'malformed JSON rejected');

echo "\nPassed: {$passed}; Failed: {$failed}\n";
exit($failed > 0 ? 1 : 0);
