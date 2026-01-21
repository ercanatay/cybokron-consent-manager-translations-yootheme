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

// Delete plugin options
delete_option('yt_consent_translations');

// For multisite, delete options from all sites
if (is_multisite()) {
    global $wpdb;
    
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        delete_option('yt_consent_translations');
        restore_current_blog();
    }
}
