<?php
/**
 * Static authorization checks for module-local AJAX pickers and state-changing actions.
 * Does not boot Perfex. Run: php modules/nj_court_search/tests/picker_auth_static_test.php
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

$controller = file_get_contents(__DIR__ . '/../controllers/Nj_court_search.php');
assert_true($controller !== false, 'controller readable');

$methods = [
    'ajax_search_leads',
    'ajax_search_customers',
    'ajax_search_contacts',
];

foreach ($methods as $method) {
    assert_true(
        strpos($controller, "function {$method}") !== false,
        "method exists: {$method}"
    );
}

assert_true(
    strpos($controller, 'function ajax_picker_guard') !== false,
    'ajax_picker_guard exists'
);
assert_true(
    strpos($controller, "nj_court_search_staff_can('create')") !== false
        && strpos($controller, 'ajax_picker_guard') !== false,
    'picker guard requires create capability'
);

// Each picker method must call ajax_picker_guard
foreach ($methods as $method) {
    if (!preg_match('/function\s+' . preg_quote($method, '/') . '\s*\(\)\s*\{([^}]+(?:\{[^}]*\}[^}]*)*)\}/s', $controller, $m)) {
        // Fallback: ensure method body contains guard call nearby
        $pos = strpos($controller, "function {$method}");
        $slice = $pos === false ? '' : substr($controller, $pos, 400);
        assert_true(strpos($slice, 'ajax_picker_guard') !== false, "{$method} calls ajax_picker_guard");
    } else {
        assert_true(strpos($m[1], 'ajax_picker_guard') !== false, "{$method} calls ajax_picker_guard");
    }
}

// State-changing actions must reject GET
foreach (['refresh', 'retry', 'cancel'] as $action) {
    $pos = strpos($controller, "function {$action}");
    $slice = $pos === false ? '' : substr($controller, $pos, 600);
    assert_true(
        strpos($slice, "method(true) !== 'POST'") !== false
            || strpos($slice, 'Method not allowed') !== false,
        "{$action} rejects non-POST"
    );
}

assert_true(
    strpos($controller, "nj_court_search_staff_can('manage_settings')") !== false,
    'settings/test_connection gated by manage_settings'
);

echo "\nPassed: {$passed}; Failed: {$failed}\n";
exit($failed > 0 ? 1 : 0);
