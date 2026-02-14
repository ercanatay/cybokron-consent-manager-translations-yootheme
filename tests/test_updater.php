<?php
// phpcs:ignoreFile -- Development-only CLI assertions.
if (!defined('ABSPATH') && PHP_SAPI === 'cli') {
	define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('ABSPATH')) {
	exit;
}

require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/includes/class-updater.php';

$failures = [];

$settings = CYBOCOMA_Updater::sanitize_settings([
	'enabled' => false,
	'channel' => 'beta',
	'check_interval' => 'hourly'
]);

if ($settings['enabled'] !== false) {
	$failures[] = 'sanitize_settings should keep explicit enabled=false.';
}

if ($settings['channel'] !== 'wordpress') {
	$failures[] = 'sanitize_settings should force wordpress channel.';
}

if ($settings['check_interval'] !== 'twicedaily') {
	$failures[] = 'sanitize_settings should force twicedaily interval.';
}

if (CYBOCOMA_Updater::normalize_tag_version('v1.2.3') !== '1.2.3') {
	$failures[] = 'normalize_tag_version should strip v prefix.';
}

if (CYBOCOMA_Updater::normalize_tag_version('release-1.2.3') !== '') {
	$failures[] = 'normalize_tag_version should reject non-semver tags.';
}

if (!CYBOCOMA_Updater::is_newer_version('9.9.9')) {
	$failures[] = 'is_newer_version should return true for higher versions.';
}

if (CYBOCOMA_Updater::is_newer_version(CYBOCOMA_VERSION)) {
	$failures[] = 'is_newer_version should return false for equal version.';
}

$GLOBALS['cybocoma_site_transient_store']['update_plugins'] = (object) [];
$checked = CYBOCOMA_Updater::check_for_updates(false);
if (!empty($checked['updateAvailable'])) {
	$failures[] = 'check_for_updates should not mark update available when metadata is missing.';
}

if ($checked['status'] !== 'up_to_date') {
	$failures[] = 'check_for_updates should default status to up_to_date when metadata is missing.';
}

$update_transient = new stdClass();
$update_transient->response = [
	CYBOCOMA_PLUGIN_BASENAME => (object) [
		'new_version' => '9.9.9'
	]
];
$GLOBALS['cybocoma_site_transient_store']['update_plugins'] = $update_transient;

$checked = CYBOCOMA_Updater::check_for_updates(false);
if (empty($checked['updateAvailable'])) {
	$failures[] = 'check_for_updates should mark updateAvailable when metadata version is newer.';
}

if ($checked['latestVersion'] !== '9.9.9') {
	$failures[] = 'check_for_updates should persist latestVersion from update metadata.';
}

if ($checked['status'] !== 'update_available') {
	$failures[] = 'check_for_updates should set status update_available when a newer version exists.';
}

$up_to_date_transient = new stdClass();
$up_to_date_transient->no_update = [
	CYBOCOMA_PLUGIN_BASENAME => (object) [
		'new_version' => CYBOCOMA_VERSION
	]
];
$GLOBALS['cybocoma_site_transient_store']['update_plugins'] = $up_to_date_transient;

$checked = CYBOCOMA_Updater::check_for_updates(false);
if ($checked['status'] !== 'up_to_date') {
	$failures[] = 'check_for_updates should set status up_to_date when no update is available.';
}

$GLOBALS['cybocoma_scheduled_events'] = [];
CYBOCOMA_Updater::update_settings(['enabled' => true]);
if (wp_next_scheduled(CYBOCOMA_Updater::CRON_HOOK) === false) {
	$failures[] = 'sync_schedule should schedule cron event when updater is enabled.';
}

CYBOCOMA_Updater::update_settings(['enabled' => false]);
if (wp_next_scheduled(CYBOCOMA_Updater::CRON_HOOK) !== false) {
	$failures[] = 'sync_schedule should clear cron event when updater is disabled.';
}

if (!empty($failures)) {
	fwrite(STDERR, "test_updater failed:\n- " . implode("\n- ", $failures) . "\n");
	exit(1);
}

echo "test_updater passed\n";
