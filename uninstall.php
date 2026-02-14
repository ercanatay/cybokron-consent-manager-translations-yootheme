<?php
/**
 * Uninstall script for Cybokron Consent Manager Translations for YOOtheme Pro
 *
 * This file runs when the plugin is uninstalled from WordPress.
 * It removes all plugin data from the database.
 *
 * @package CYBOCOMA_Consent_Translations
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

// Clear scheduled cron events
$cybocoma_cron_hook = 'cybocoma_updater_cron_check';
if (function_exists('wp_unschedule_hook')) {
	wp_unschedule_hook($cybocoma_cron_hook);
} else {
	$cybocoma_cron_ts = wp_next_scheduled($cybocoma_cron_hook);
	while ($cybocoma_cron_ts) {
		wp_unschedule_event($cybocoma_cron_ts, $cybocoma_cron_hook);
		$cybocoma_cron_ts = wp_next_scheduled($cybocoma_cron_hook);
	}
}

// Delete legacy plugin option
delete_option('cybocoma_consent_translations');
delete_option('cybocoma_health_report');
delete_option('cybocoma_updater_settings');
delete_option('cybocoma_updater_state');

/**
 * Delete locale-scoped options and snapshot options.
 *
 * @return void
 */
function cybocoma_delete_scoped_options() {
	global $wpdb;

	$patterns = [
		'cybocoma_consent_translations__%',
		'cybocoma_consent_translations_snapshots__%'
	];

	foreach ($patterns as $pattern) {
		$escaped = $wpdb->esc_like(str_replace('%', '', $pattern)) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time uninstall cleanup of wildcard option names.
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$escaped
			)
		);

		if (!is_array($rows)) {
			continue;
		}

		foreach ($rows as $option_name) {
			delete_option($option_name);
		}
	}
}

cybocoma_delete_scoped_options();

// For multisite, delete options from all sites
if (is_multisite()) {
	// Using get_sites() for better compatibility with modern WordPress
	$cybocoma_sites = get_sites(['fields' => 'ids']);

	foreach ($cybocoma_sites as $cybocoma_blog_id) {
		switch_to_blog($cybocoma_blog_id);
		if (function_exists('wp_unschedule_hook')) {
			wp_unschedule_hook($cybocoma_cron_hook);
		} else {
			$cybocoma_cron_ts = wp_next_scheduled($cybocoma_cron_hook);
			while ($cybocoma_cron_ts) {
				wp_unschedule_event($cybocoma_cron_ts, $cybocoma_cron_hook);
				$cybocoma_cron_ts = wp_next_scheduled($cybocoma_cron_hook);
			}
		}
		delete_option('cybocoma_consent_translations');
		delete_option('cybocoma_health_report');
		delete_option('cybocoma_updater_settings');
		delete_option('cybocoma_updater_state');
		cybocoma_delete_scoped_options();
		restore_current_blog();
	}
}
