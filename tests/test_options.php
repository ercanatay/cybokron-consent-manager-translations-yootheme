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
require_once dirname(__DIR__) . '/includes/class-options.php';

$failures = [];

$input = [
	'enabled' => false,
	'language' => 'tr',
	'custom_strings' => [
		'banner_text' => 'Custom banner',
		'unknown_key' => 'ignored'
	]
];

$stored = CYBOCOMA_Options::update_options($input, 'tr_TR', 'test_save');

if ($stored['language'] !== 'tr') {
	$failures[] = 'Language should remain tr.';
}

if (!isset($stored['custom_strings']['banner_text'])) {
	$failures[] = 'Expected custom banner_text to persist.';
}

if (isset($stored['custom_strings']['unknown_key'])) {
	$failures[] = 'Unknown keys must be stripped from custom_strings.';
}

$readBack = CYBOCOMA_Options::get_options('tr_TR');
if ($readBack['enabled'] !== false) {
	$failures[] = 'Enabled should be false after update.';
}

$snapshots = CYBOCOMA_Options::get_snapshots('tr_TR');
if (count($snapshots) < 1) {
	$failures[] = 'At least one snapshot should be created after update.';
}

$restoreSource = [
	'enabled' => true,
	'language' => 'en',
	'custom_strings' => [
		'button_accept' => 'Allow'
	]
];
CYBOCOMA_Options::update_options($restoreSource, 'tr_TR', 'second_save');
$snapshots = CYBOCOMA_Options::get_snapshots('tr_TR');
$oldSnapshotId = isset($snapshots[1]['id']) ? $snapshots[1]['id'] : '';

if ($oldSnapshotId === '') {
	$failures[] = 'Expected at least two snapshots to test restore.';
} else {
	$restored = CYBOCOMA_Options::restore_snapshot($oldSnapshotId, 'tr_TR');
	if (!is_array($restored) || $restored['language'] !== 'tr') {
		$failures[] = 'Snapshot restore should restore older state.';
	}
}

if (!empty($failures)) {
	fwrite(STDERR, "test_options failed:\n- " . implode("\n- ", $failures) . "\n");
	exit(1);
}

echo "test_options passed\n";
