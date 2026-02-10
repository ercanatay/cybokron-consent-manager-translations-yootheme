<?php
/**
 * WordPress core update status integration for admin UI.
 *
 * @package YT_Consent_Translations
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class YTCT_Updater
 */
class YTCT_Updater {

	/**
	 * Option key for updater settings.
	 */
	const SETTINGS_OPTION = 'ytct_updater_settings';

	/**
	 * Option key for updater state.
	 */
	const STATE_OPTION = 'ytct_updater_state';

	/**
	 * Cron hook name.
	 */
	const CRON_HOOK = 'ytct_updater_cron_check';

	/**
	 * Boot guard.
	 *
	 * @var bool
	 */
	private static $booted = false;

	/**
	 * Register updater hooks.
	 *
	 * @return void
	 */
	public static function boot() {
		if (self::$booted) {
			return;
		}

		self::$booted = true;
		add_action('init', [__CLASS__, 'sync_schedule']);
		add_action(self::CRON_HOOK, [__CLASS__, 'cron_check']);
	}

	/**
	 * Handle plugin activation lifecycle.
	 *
	 * @return void
	 */
	public static function on_activation() {
		self::get_settings();
		self::get_state();
		self::sync_schedule();
		self::check_for_updates(true);
	}

	/**
	 * Handle plugin deactivation lifecycle.
	 *
	 * @return void
	 */
	public static function on_deactivation() {
		self::clear_schedule();
	}

	/**
	 * Get default updater settings.
	 *
	 * @return array
	 */
	public static function get_default_settings() {
		return [
			'enabled' => true,
			'channel' => 'wordpress',
			'check_interval' => 'twicedaily'
		];
	}

	/**
	 * Sanitize updater settings.
	 *
	 * @param mixed $settings Settings payload.
	 * @return array
	 */
	public static function sanitize_settings($settings) {
		$defaults = self::get_default_settings();
		if (!is_array($settings)) {
			return $defaults;
		}

		return [
			'enabled' => isset($settings['enabled']) ? (bool) $settings['enabled'] : true,
			'channel' => 'wordpress',
			'check_interval' => 'twicedaily'
		];
	}

	/**
	 * Get persisted settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$raw = get_option(self::SETTINGS_OPTION, []);
		$settings = self::sanitize_settings($raw);

		if (!is_array($raw) || $settings !== $raw) {
			update_option(self::SETTINGS_OPTION, $settings, false);
		}

		return $settings;
	}

	/**
	 * Update settings.
	 *
	 * @param array $settings Partial settings payload.
	 * @return array
	 */
	public static function update_settings($settings) {
		$current = self::get_settings();
		$settings = is_array($settings) ? $settings : [];
		$next = self::sanitize_settings(array_merge($current, $settings));
		update_option(self::SETTINGS_OPTION, $next, false);
		self::sync_schedule();
		return $next;
	}

	/**
	 * Check if updater checks are enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$settings = self::get_settings();
		return !empty($settings['enabled']);
	}

	/**
	 * Get default state payload.
	 *
	 * @return array
	 */
	public static function get_default_state() {
		return [
			'last_checked_at' => '',
			'status' => 'idle',
			'latest_version' => '',
			'latest_tag' => '',
			'update_available' => false,
			'last_error' => '',
			'last_error_at' => '',
			'last_install_at' => '',
			'last_installed_version' => ''
		];
	}

	/**
	 * Sanitize state payload.
	 *
	 * @param mixed $state State payload.
	 * @return array
	 */
	public static function sanitize_state($state) {
		$defaults = self::get_default_state();
		if (!is_array($state)) {
			return $defaults;
		}

		$status = isset($state['status']) ? sanitize_key((string) $state['status']) : 'idle';
		$allowed_status = [
			'idle' => true,
			'up_to_date' => true,
			'update_available' => true,
			'error' => true,
			'installing' => true,
			'updated' => true,
			'update_failed' => true
		];

		if (!isset($allowed_status[$status])) {
			$status = 'idle';
		}

		return [
			'last_checked_at' => isset($state['last_checked_at']) ? sanitize_text_field((string) $state['last_checked_at']) : '',
			'status' => $status,
			'latest_version' => isset($state['latest_version']) ? sanitize_text_field((string) $state['latest_version']) : '',
			'latest_tag' => isset($state['latest_tag']) ? sanitize_text_field((string) $state['latest_tag']) : '',
			'update_available' => !empty($state['update_available']),
			'last_error' => isset($state['last_error']) ? sanitize_text_field((string) $state['last_error']) : '',
			'last_error_at' => isset($state['last_error_at']) ? sanitize_text_field((string) $state['last_error_at']) : '',
			'last_install_at' => isset($state['last_install_at']) ? sanitize_text_field((string) $state['last_install_at']) : '',
			'last_installed_version' => isset($state['last_installed_version']) ? sanitize_text_field((string) $state['last_installed_version']) : ''
		];
	}

	/**
	 * Get persisted updater state.
	 *
	 * @return array
	 */
	public static function get_state() {
		$raw = get_option(self::STATE_OPTION, []);
		$state = self::sanitize_state($raw);

		if (!is_array($raw) || $state !== $raw) {
			update_option(self::STATE_OPTION, $state, false);
		}

		return $state;
	}

	/**
	 * Update state fields.
	 *
	 * @param array $changes State changes.
	 * @return array
	 */
	public static function update_state($changes) {
		$current = self::get_state();
		$changes = is_array($changes) ? $changes : [];
		$next = self::sanitize_state(array_merge($current, $changes));
		update_option(self::STATE_OPTION, $next, false);
		return $next;
	}

	/**
	 * Normalize semantic version from a tag-like string.
	 *
	 * @param string $tag Version or tag value.
	 * @return string
	 */
	public static function normalize_tag_version($tag) {
		$tag = trim((string) $tag);
		if ($tag === '') {
			return '';
		}

		$tag = preg_replace('/^[vV]/', '', $tag);
		if (!is_string($tag) || !preg_match('/^\d+(?:\.\d+)*$/', $tag)) {
			return '';
		}

		return $tag;
	}

	/**
	 * Check whether the release version is newer than current plugin version.
	 *
	 * @param string $remote_version Remote version.
	 * @return bool
	 */
	public static function is_newer_version($remote_version) {
		$remote_version = trim((string) $remote_version);
		if ($remote_version === '') {
			return false;
		}

		return version_compare($remote_version, YTCT_VERSION, '>');
	}

	/**
	 * Run update check and persist state.
	 *
	 * @param bool $force Force immediate WordPress update metadata refresh.
	 * @return array
	 */
	public static function check_for_updates($force = false) {
		$changes = [
			'last_checked_at' => gmdate('c'),
			'last_error' => '',
			'last_error_at' => ''
		];

		$item = self::get_core_update_item($force);
		$version = self::extract_item_version($item);
		if ($version === '') {
			$changes['latest_version'] = YTCT_VERSION;
			$changes['latest_tag'] = YTCT_VERSION;
			$changes['update_available'] = false;
			$changes['status'] = 'up_to_date';
			self::update_state($changes);
			return self::get_admin_payload();
		}

		$changes['latest_version'] = $version;
		$changes['latest_tag'] = $version;
		$changes['update_available'] = self::is_newer_version($version);
		$changes['status'] = !empty($changes['update_available']) ? 'update_available' : 'up_to_date';
		self::update_state($changes);

		return self::get_admin_payload();
	}

	/**
	 * Run immediate check from admin action.
	 *
	 * @return array
	 */
	public static function manual_check() {
		$payload = self::check_for_updates(true);
		$payload['installationAttempted'] = false;
		$payload['installationSucceeded'] = false;
		return $payload;
	}

	/**
	 * Get updater payload intended for admin UI.
	 *
	 * @return array
	 */
	public static function get_admin_payload() {
		$settings = self::get_settings();
		$state = self::get_state();

		return [
			'enabled' => !empty($settings['enabled']),
			'channel' => 'wordpress',
			'checkInterval' => 'twicedaily',
			'currentVersion' => YTCT_VERSION,
			'latestVersion' => $state['latest_version'] !== '' ? $state['latest_version'] : YTCT_VERSION,
			'latestTag' => $state['latest_tag'] !== '' ? $state['latest_tag'] : YTCT_VERSION,
			'updateAvailable' => !empty($state['update_available']),
			'status' => $state['status'],
			'statusLabel' => self::get_status_label($state['status']),
			'lastCheckedAt' => $state['last_checked_at'],
			'lastError' => $state['last_error'],
			'lastErrorAt' => $state['last_error_at'],
			'lastInstallAt' => $state['last_install_at'],
			'lastInstalledVersion' => $state['last_installed_version']
		];
	}

	/**
	 * Convert updater status key into a readable translated label.
	 *
	 * @param string $status Status key.
	 * @return string
	 */
	public static function get_status_label($status) {
		$status = sanitize_key((string) $status);
		$labels = [
			'idle' => __('Idle', 'yt-consent-translations-main'),
			'up_to_date' => __('Up to date', 'yt-consent-translations-main'),
			'update_available' => __('Update available', 'yt-consent-translations-main'),
			'error' => __('Error', 'yt-consent-translations-main'),
			'installing' => __('Installing', 'yt-consent-translations-main'),
			'updated' => __('Updated', 'yt-consent-translations-main'),
			'update_failed' => __('Update failed', 'yt-consent-translations-main')
		];

		return isset($labels[$status]) ? $labels[$status] : $labels['idle'];
	}

	/**
	 * Schedule or clear cron based on updater settings.
	 *
	 * @return void
	 */
	public static function sync_schedule() {
		if (!function_exists('wp_next_scheduled') || !function_exists('wp_schedule_event') || !function_exists('wp_unschedule_event')) {
			return;
		}

		if (!self::is_enabled()) {
			self::clear_schedule();
			return;
		}

		if (!wp_next_scheduled(self::CRON_HOOK)) {
			wp_schedule_event(time() + 300, 'twicedaily', self::CRON_HOOK);
		}
	}

	/**
	 * Clear all scheduled updater events.
	 *
	 * @return void
	 */
	public static function clear_schedule() {
		if (!function_exists('wp_next_scheduled') || !function_exists('wp_unschedule_event')) {
			return;
		}

		$timestamp = wp_next_scheduled(self::CRON_HOOK);
		while ($timestamp) {
			wp_unschedule_event($timestamp, self::CRON_HOOK);
			$timestamp = wp_next_scheduled(self::CRON_HOOK);
		}
	}

	/**
	 * Cron callback: refresh status data.
	 *
	 * @return void
	 */
	public static function cron_check() {
		if (!self::is_enabled()) {
			return;
		}

		self::check_for_updates(false);
	}

	/**
	 * Read plugin update item from WordPress core update metadata.
	 *
	 * @param bool $force Force immediate core refresh.
	 * @return object|array|null
	 */
	private static function get_core_update_item($force = false) {
		if ($force && function_exists('wp_update_plugins')) {
			wp_update_plugins();
		}

		if (!function_exists('get_site_transient')) {
			return null;
		}

		$transient = get_site_transient('update_plugins');
		if (!is_object($transient)) {
			return null;
		}

		if (isset($transient->response) && is_array($transient->response) && isset($transient->response[YTCT_PLUGIN_BASENAME])) {
			return $transient->response[YTCT_PLUGIN_BASENAME];
		}

		if (isset($transient->no_update) && is_array($transient->no_update) && isset($transient->no_update[YTCT_PLUGIN_BASENAME])) {
			return $transient->no_update[YTCT_PLUGIN_BASENAME];
		}

		return null;
	}

	/**
	 * Extract version value from a WordPress update item.
	 *
	 * @param mixed $item Update item.
	 * @return string
	 */
	private static function extract_item_version($item) {
		$version = '';

		if (is_object($item) && isset($item->new_version)) {
			$version = (string) $item->new_version;
		} elseif (is_array($item) && isset($item['new_version'])) {
			$version = (string) $item['new_version'];
		}

		$normalized = self::normalize_tag_version($version);
		if ($normalized !== '') {
			return $normalized;
		}

		$version = sanitize_text_field($version);
		if ($version === '') {
			return '';
		}

		return $version;
	}
}
