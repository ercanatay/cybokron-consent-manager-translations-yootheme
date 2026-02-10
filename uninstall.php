<?php
/**
 * Uninstall script for YT Consent Translations
 *
 * This file runs when the plugin is uninstalled from WordPress.
 * It removes all plugin data from the database.
 *
 * @package YT_Consent_Translations
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

// Clear scheduled cron events
$ytct_cron_hook = 'ytct_updater_cron_check';
if (function_exists('wp_unschedule_hook')) {
	wp_unschedule_hook($ytct_cron_hook);
} else {
	$ytct_cron_ts = wp_next_scheduled($ytct_cron_hook);
	while ($ytct_cron_ts) {
		wp_unschedule_event($ytct_cron_ts, $ytct_cron_hook);
		$ytct_cron_ts = wp_next_scheduled($ytct_cron_hook);
	}
}

// Delete legacy plugin option
delete_option('yt_consent_translations');
delete_option('ytct_health_report');
delete_option('ytct_updater_settings');
delete_option('ytct_updater_state');

/**
 * Delete locale-scoped options and snapshot options.
 *
 * @return void
 */
function ytct_delete_scoped_options() {
	global $wpdb;

	$patterns = [
		'yt_consent_translations__%',
		'yt_consent_translations_snapshots__%'
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

ytct_delete_scoped_options();

// For multisite, delete options from all sites
if (is_multisite()) {
	// Using get_sites() for better compatibility with modern WordPress
	$ytct_sites = get_sites(['fields' => 'ids']);

	foreach ($ytct_sites as $ytct_blog_id) {
		switch_to_blog($ytct_blog_id);
		if (function_exists('wp_unschedule_hook')) {
			wp_unschedule_hook($ytct_cron_hook);
		} else {
			$ytct_cron_ts = wp_next_scheduled($ytct_cron_hook);
			while ($ytct_cron_ts) {
				wp_unschedule_event($ytct_cron_ts, $ytct_cron_hook);
				$ytct_cron_ts = wp_next_scheduled($ytct_cron_hook);
			}
		}
		delete_option('yt_consent_translations');
		delete_option('ytct_health_report');
		delete_option('ytct_updater_settings');
		delete_option('ytct_updater_state');
		ytct_delete_scoped_options();
		restore_current_blog();
	}
}
