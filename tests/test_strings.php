<?php
// phpcs:ignoreFile -- Development-only CLI assertions.
if (!defined('ABSPATH') && PHP_SAPI === 'cli') {
	define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('ABSPATH')) {
	exit;
}

require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/includes/class-strings.php';

$failures = [];

if (CYBOCOMA_Strings::detect_language_from_locale('tr_TR') !== 'tr') {
	$failures[] = 'Expected tr_TR => tr';
}

if (CYBOCOMA_Strings::detect_language_from_locale('en-US') !== 'en') {
	$failures[] = 'Expected en-US => en';
}

if (CYBOCOMA_Strings::detect_language_from_locale('xx_YY') !== 'en') {
	$failures[] = 'Expected unknown locale fallback to en';
}

if (!CYBOCOMA_Strings::is_valid_language('auto')) {
	$failures[] = 'Expected auto to be a valid language option';
}

if (CYBOCOMA_Strings::resolve_language_code('invalid') !== 'en') {
	$failures[] = 'Expected invalid language fallback to en';
}

if (CYBOCOMA_Strings::resolve_language_code('auto', true) !== 'en') {
	$failures[] = 'Expected auto with resolve flag to map from locale';
}

if (!empty($failures)) {
	fwrite(STDERR, "test_strings failed:\n- " . implode("\n- ", $failures) . "\n");
	exit(1);
}

echo "test_strings passed\n";
