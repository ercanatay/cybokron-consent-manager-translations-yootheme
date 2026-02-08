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

// Delete legacy plugin option
delete_option('yt_consent_translations');
delete_option('ytct_health_report');

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
		$escaped = esc_sql($wpdb->esc_like(str_replace('%', '', $pattern))) . '%';
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
		delete_option('yt_consent_translations');
		delete_option('ytct_health_report');
		ytct_delete_scoped_options();
		restore_current_blog();
	}
}
