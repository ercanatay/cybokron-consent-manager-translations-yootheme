<?php
/**
 * GitHub-backed updater integration for plugin auto updates.
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
	 * Cached release transient key.
	 */
	const RELEASE_TRANSIENT = 'ytct_updater_latest_release';

	/**
	 * GitHub latest release endpoint.
	 */
	const RELEASE_ENDPOINT = 'https://api.github.com/repos/ercanatay/yt-consent-translations/releases/latest';

	/**
	 * Repository URL.
	 */
	const REPOSITORY_URL = 'https://github.com/ercanatay/yt-consent-translations';

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
		add_filter('pre_set_site_transient_update_plugins', [__CLASS__, 'inject_update_transient']);
		add_filter('plugins_api', [__CLASS__, 'filter_plugins_api'], 20, 3);
		add_filter('auto_update_plugin', [__CLASS__, 'filter_auto_update_plugin'], 10, 2);
		add_filter('upgrader_source_selection', [__CLASS__, 'normalize_upgrader_source'], 10, 4);
		add_action('upgrader_process_complete', [__CLASS__, 'handle_upgrader_complete'], 10, 2);
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
			'channel' => 'stable',
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
			'channel' => 'stable',
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
			update_option(self::SETTINGS_OPTION, $settings);
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
		update_option(self::SETTINGS_OPTION, $next);
		self::sync_schedule();
		return $next;
	}

	/**
	 * Check if updater channel is enabled.
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
			update_option(self::STATE_OPTION, $state);
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
		update_option(self::STATE_OPTION, $next);
		return $next;
	}

	/**
	 * Normalize semantic version from release tag.
	 *
	 * @param string $tag Git tag value.
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
	 * Build release metadata from GitHub API payload.
	 *
	 * @param array $payload Decoded API payload.
	 * @return array|null
	 */
	public static function build_release_from_payload($payload) {
		if (!is_array($payload)) {
			return null;
		}

		if (!empty($payload['draft']) || !empty($payload['prerelease'])) {
			return null;
		}

		$tag = isset($payload['tag_name']) ? sanitize_text_field((string) $payload['tag_name']) : '';
		$version = self::normalize_tag_version($tag);
		if ($version === '') {
			return null;
		}

		$package_url = '';
		if (isset($payload['assets']) && is_array($payload['assets'])) {
			foreach ($payload['assets'] as $asset) {
				if (!is_array($asset) || empty($asset['browser_download_url'])) {
					continue;
				}

				$candidate = esc_url_raw((string) $asset['browser_download_url']);
				if (is_string($candidate) && $candidate !== '') {
					$package_url = $candidate;
					break;
				}
			}
		}

		if ($package_url === '' && !empty($payload['zipball_url'])) {
			$package_url = esc_url_raw((string) $payload['zipball_url']);
		}

		if (!is_string($package_url) || $package_url === '') {
			return null;
		}

		return [
			'tag' => $tag,
			'version' => $version,
			'package_url' => $package_url,
			'html_url' => !empty($payload['html_url']) ? esc_url_raw((string) $payload['html_url']) : self::REPOSITORY_URL,
			'name' => isset($payload['name']) ? sanitize_text_field((string) $payload['name']) : $tag,
			'body' => isset($payload['body']) && is_scalar($payload['body']) ? (string) $payload['body'] : '',
			'published_at' => isset($payload['published_at']) ? sanitize_text_field((string) $payload['published_at']) : ''
		];
	}

	/**
	 * Get latest release metadata.
	 *
	 * @param bool $force Force refresh from API.
	 * @return array|null
	 */
	public static function get_latest_release($force = false) {
		if (!$force) {
			$cached = get_transient(self::RELEASE_TRANSIENT);
			if (is_array($cached) && !empty($cached['version']) && !empty($cached['package_url'])) {
				return $cached;
			}
		}

		$response = wp_remote_get(
			self::RELEASE_ENDPOINT,
			[
				'timeout' => 15,
				'headers' => [
					'Accept' => 'application/vnd.github+json',
					'User-Agent' => self::build_user_agent()
				]
			]
		);

		if (self::is_wp_error($response)) {
			self::update_state([
				'status' => 'error',
				'last_error' => self::extract_error_message($response),
				'last_error_at' => gmdate('c')
			]);
			return null;
		}

		$code = self::get_response_code($response);
		if ($code < 200 || $code >= 300) {
			self::update_state([
				'status' => 'error',
				'last_error' => sprintf('GitHub API request failed with status %d.', (int) $code),
				'last_error_at' => gmdate('c')
			]);
			return null;
		}

		$body = self::get_response_body($response);
		$data = json_decode($body, true);
		if (!is_array($data)) {
			self::update_state([
				'status' => 'error',
				'last_error' => __('Invalid response from GitHub releases API.', 'yt-consent-translations-1.3.5'),
				'last_error_at' => gmdate('c')
			]);
			return null;
		}

		$release = self::build_release_from_payload($data);
		if (!is_array($release)) {
			self::update_state([
				'status' => 'error',
				'last_error' => __('No valid stable release found in GitHub response.', 'yt-consent-translations-1.3.5'),
				'last_error_at' => gmdate('c')
			]);
			return null;
		}

		$ttl = defined('HOUR_IN_SECONDS') ? 12 * HOUR_IN_SECONDS : 43200;
		set_transient(self::RELEASE_TRANSIENT, $release, $ttl);
		return $release;
	}

	/**
	 * Run update check and persist state.
	 *
	 * @param bool $force Force GitHub API refresh.
	 * @return array
	 */
	public static function check_for_updates($force = false) {
		$release = self::get_latest_release($force);
		$changes = [
			'last_checked_at' => gmdate('c')
		];

		if (!is_array($release)) {
			$changes['status'] = 'error';
			$changes['update_available'] = false;
			self::update_state($changes);
			return self::get_admin_payload();
		}

		$update_available = self::is_newer_version($release['version']);
		$changes['latest_version'] = $release['version'];
		$changes['latest_tag'] = $release['tag'];
		$changes['update_available'] = $update_available;
		$changes['status'] = $update_available ? 'update_available' : 'up_to_date';
		$changes['last_error'] = '';
		$changes['last_error_at'] = '';

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
		$attempted_install = false;
		$installed = false;

		if (self::is_enabled() && !empty($payload['updateAvailable'])) {
			$attempted_install = true;
			$installed = self::install_latest_update();
			$payload = self::get_admin_payload();
		}

		$payload['installationAttempted'] = $attempted_install;
		$payload['installationSucceeded'] = $installed;
		return $payload;
	}

	/**
	 * Inject update data into WordPress plugin update transient.
	 *
	 * @param mixed $transient Existing transient payload.
	 * @return mixed
	 */
	public static function inject_update_transient($transient) {
		if (!is_object($transient)) {
			$transient = new stdClass();
		}

		if (!isset($transient->response) || !is_array($transient->response)) {
			$transient->response = [];
		}

		if (!isset($transient->no_update) || !is_array($transient->no_update)) {
			$transient->no_update = [];
		}

		if (!self::is_enabled()) {
			unset($transient->response[YTCT_PLUGIN_BASENAME]);
			unset($transient->no_update[YTCT_PLUGIN_BASENAME]);
			return $transient;
		}

		$release = self::get_latest_release(false);
		if (!is_array($release)) {
			return $transient;
		}

		$item = self::build_update_item($release);
		if (self::is_newer_version($release['version'])) {
			$transient->response[YTCT_PLUGIN_BASENAME] = $item;
			unset($transient->no_update[YTCT_PLUGIN_BASENAME]);
		} else {
			$item->new_version = YTCT_VERSION;
			$transient->no_update[YTCT_PLUGIN_BASENAME] = $item;
			unset($transient->response[YTCT_PLUGIN_BASENAME]);
		}

		return $transient;
	}

	/**
	 * Provide plugin information details to WordPress upgrader UI.
	 *
	 * @param false|object|array $result Existing result.
	 * @param string             $action API action.
	 * @param object             $args Request args.
	 * @return object|array|false
	 */
	public static function filter_plugins_api($result, $action, $args) {
		if ($action !== 'plugin_information' || !is_object($args)) {
			return $result;
		}

		$slug = isset($args->slug) ? (string) $args->slug : '';
		$plugin_slug = dirname(YTCT_PLUGIN_BASENAME);
		if ($slug !== $plugin_slug) {
			return $result;
		}

		$release = self::get_latest_release(false);
		$release_version = is_array($release) && isset($release['version']) ? (string) $release['version'] : YTCT_VERSION;
		$release_url = is_array($release) && !empty($release['html_url']) ? (string) $release['html_url'] : self::REPOSITORY_URL;
		$release_package = is_array($release) && !empty($release['package_url']) ? (string) $release['package_url'] : '';
		$release_body = is_array($release) && isset($release['body']) ? (string) $release['body'] : '';

		return (object) [
			'name' => 'YT Consent Translations',
			'slug' => $plugin_slug,
			'version' => $release_version,
			'author' => '<a href="https://www.ercanatay.com/en/">Ercan ATAY</a>',
			'author_profile' => 'https://www.ercanatay.com/en/',
			'requires' => '5.0',
			'tested' => '',
			'requires_php' => '7.4',
			'homepage' => self::REPOSITORY_URL,
			'download_link' => $release_package,
			'last_updated' => is_array($release) && !empty($release['published_at']) ? (string) $release['published_at'] : '',
			'sections' => [
				'description' => __('GitHub stable update channel is enabled for this plugin.', 'yt-consent-translations-1.3.5'),
				'changelog' => $release_body,
				'homepage' => $release_url
			],
			'external' => true
		];
	}

	/**
	 * Determine plugin auto update behavior.
	 *
	 * @param bool  $update Current auto update flag.
	 * @param mixed $item Plugin update item.
	 * @return bool
	 */
	public static function filter_auto_update_plugin($update, $item) {
		if (!is_object($item) || !isset($item->plugin)) {
			return $update;
		}

		if ((string) $item->plugin !== YTCT_PLUGIN_BASENAME) {
			return $update;
		}

		return self::is_enabled();
	}

	/**
	 * Normalize GitHub zipball extraction folder to plugin slug.
	 *
	 * @param string       $source Downloaded source directory.
	 * @param string       $remote_source Remote source root.
	 * @param WP_Upgrader  $upgrader Upgrader instance.
	 * @param array        $hook_extra Upgrader hook extras.
	 * @return string|WP_Error
	 */
	public static function normalize_upgrader_source($source, $remote_source, $upgrader, $hook_extra) {
		if (!self::is_target_plugin_hook_extra($hook_extra)) {
			return $source;
		}

		$expected_dir = dirname(YTCT_PLUGIN_BASENAME);
		$target = self::join_path($remote_source, $expected_dir);
		if (self::normalize_path($source) === self::normalize_path($target)) {
			return $source;
		}

		if (!is_dir($source)) {
			return $source;
		}

		$moved = false;
		global $wp_filesystem;
		if (is_object($wp_filesystem) && method_exists($wp_filesystem, 'move')) {
			$moved = $wp_filesystem->move($source, $target, true);
		} elseif (function_exists('rename')) {
			$moved = @rename($source, $target);
		}

		if (!$moved) {
			if (class_exists('WP_Error')) {
				return new WP_Error('ytct_updater_source_rename_failed', __('Could not normalize update package directory.', 'yt-consent-translations-1.3.5'));
			}
			return $source;
		}

		return $target;
	}

	/**
	 * Schedule or clear cron based on channel state.
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
	 * Cron callback: check and auto-install if available.
	 *
	 * @return void
	 */
	public static function cron_check() {
		if (!self::is_enabled()) {
			return;
		}

		$payload = self::check_for_updates(true);
		if (!empty($payload['updateAvailable'])) {
			self::install_latest_update();
		}
	}

	/**
	 * Install latest release using WordPress upgrader.
	 *
	 * @return bool
	 */
	public static function install_latest_update() {
		$release = self::get_latest_release(true);
		if (!is_array($release) || !self::is_newer_version($release['version'])) {
			return false;
		}

		self::update_state([
			'status' => 'installing',
			'latest_version' => $release['version'],
			'latest_tag' => $release['tag'],
			'update_available' => true,
			'last_error' => '',
			'last_error_at' => ''
		]);

		$transient = get_site_transient('update_plugins');
		if (!is_object($transient)) {
			$transient = new stdClass();
		}
		if (!isset($transient->response) || !is_array($transient->response)) {
			$transient->response = [];
		}
		$transient->response[YTCT_PLUGIN_BASENAME] = self::build_update_item($release);
		set_site_transient('update_plugins', $transient);

		self::load_upgrader_dependencies();
		if (!class_exists('Plugin_Upgrader') || !class_exists('Automatic_Upgrader_Skin')) {
			self::update_state([
				'status' => 'update_failed',
				'last_error' => __('WordPress upgrader classes are not available.', 'yt-consent-translations-1.3.5'),
				'last_error_at' => gmdate('c')
			]);
			return false;
		}

		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);
		$result = $upgrader->upgrade(YTCT_PLUGIN_BASENAME);

		if (self::is_wp_error($result) || $result === false) {
			self::update_state([
				'status' => 'update_failed',
				'last_error' => self::extract_error_message($result),
				'last_error_at' => gmdate('c')
			]);
			return false;
		}

		$installed_version = self::read_installed_plugin_version();
		self::update_state([
			'status' => 'updated',
			'update_available' => false,
			'last_error' => '',
			'last_error_at' => '',
			'last_install_at' => gmdate('c'),
			'last_installed_version' => $installed_version !== '' ? $installed_version : $release['version'],
			'latest_version' => $release['version'],
			'latest_tag' => $release['tag']
		]);

		delete_transient(self::RELEASE_TRANSIENT);
		if (function_exists('wp_clean_plugins_cache')) {
			wp_clean_plugins_cache(true);
		}

		return true;
	}

	/**
	 * Handle completion of plugin update operations.
	 *
	 * @param mixed $upgrader Upgrader instance.
	 * @param array $hook_extra Hook metadata.
	 * @return void
	 */
	public static function handle_upgrader_complete($upgrader, $hook_extra) {
		if (!self::is_target_plugin_hook_extra($hook_extra)) {
			return;
		}

		$installed_version = self::read_installed_plugin_version();
		self::update_state([
			'status' => 'updated',
			'update_available' => false,
			'last_error' => '',
			'last_error_at' => '',
			'last_install_at' => gmdate('c'),
			'last_installed_version' => $installed_version
		]);
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
			'channel' => 'stable',
			'checkInterval' => 'twicedaily',
			'currentVersion' => YTCT_VERSION,
			'latestVersion' => $state['latest_version'],
			'latestTag' => $state['latest_tag'],
			'updateAvailable' => !empty($state['update_available']),
			'status' => $state['status'],
			'lastCheckedAt' => $state['last_checked_at'],
			'lastError' => $state['last_error'],
			'lastErrorAt' => $state['last_error_at'],
			'lastInstallAt' => $state['last_install_at'],
			'lastInstalledVersion' => $state['last_installed_version']
		];
	}

	/**
	 * Build updater user agent value.
	 *
	 * @return string
	 */
	private static function build_user_agent() {
		$home = function_exists('home_url') ? home_url('/') : '';
		return sprintf('YT Consent Translations/%s (%s)', YTCT_VERSION, $home);
	}

	/**
	 * Build a plugin update item for transient payload.
	 *
	 * @param array $release Release metadata.
	 * @return object
	 */
	private static function build_update_item($release) {
		$item = new stdClass();
		$item->id = 'github.com/ercanatay/yt-consent-translations';
		$item->slug = dirname(YTCT_PLUGIN_BASENAME);
		$item->plugin = YTCT_PLUGIN_BASENAME;
		$item->new_version = isset($release['version']) ? (string) $release['version'] : YTCT_VERSION;
		$item->url = isset($release['html_url']) ? (string) $release['html_url'] : self::REPOSITORY_URL;
		$item->package = isset($release['package_url']) ? (string) $release['package_url'] : '';
		$item->requires = '5.0';
		$item->requires_php = '7.4';
		$item->icons = [];
		$item->banners = [];
		$item->banners_rtl = [];
		$item->tested = '';
		$item->compatibility = new stdClass();
		return $item;
	}

	/**
	 * Determine whether upgrader metadata targets this plugin.
	 *
	 * @param mixed $hook_extra Hook metadata.
	 * @return bool
	 */
	private static function is_target_plugin_hook_extra($hook_extra) {
		if (!is_array($hook_extra)) {
			return false;
		}

		if (isset($hook_extra['type']) && $hook_extra['type'] !== 'plugin') {
			return false;
		}

		if (isset($hook_extra['plugin'])) {
			return (string) $hook_extra['plugin'] === YTCT_PLUGIN_BASENAME;
		}

		if (isset($hook_extra['plugins']) && is_array($hook_extra['plugins'])) {
			$plugin_lookup = array_fill_keys($hook_extra['plugins'], true);
			return isset($plugin_lookup[YTCT_PLUGIN_BASENAME]);
		}

		return false;
	}

	/**
	 * Load WordPress upgrader dependency files.
	 *
	 * @return void
	 */
	private static function load_upgrader_dependencies() {
		$required = [
			ABSPATH . 'wp-admin/includes/file.php',
			ABSPATH . 'wp-admin/includes/misc.php',
			ABSPATH . 'wp-admin/includes/class-wp-upgrader.php',
			ABSPATH . 'wp-admin/includes/plugin.php'
		];

		foreach ($required as $file) {
			if (!is_string($file) || $file === '' || !file_exists($file)) {
				continue;
			}
			require_once $file;
		}
	}

	/**
	 * Read installed plugin version from main plugin file.
	 *
	 * @return string
	 */
	private static function read_installed_plugin_version() {
		$main_file = YTCT_PLUGIN_DIR . basename(YTCT_PLUGIN_BASENAME);
		if (!is_string($main_file) || !file_exists($main_file)) {
			return '';
		}

		if (function_exists('get_file_data')) {
			$data = get_file_data(
				$main_file,
				[
					'Version' => 'Version'
				],
				'plugin'
			);

			if (is_array($data) && isset($data['Version']) && is_string($data['Version'])) {
				return trim($data['Version']);
			}
		}

		$content = @file_get_contents($main_file);
		if (!is_string($content)) {
			return '';
		}

		if (preg_match('/^\s*\*\s*Version:\s*(.+)$/mi', $content, $matches) && isset($matches[1])) {
			return trim((string) $matches[1]);
		}

		return '';
	}

	/**
	 * Safely get response code from remote response payload.
	 *
	 * @param mixed $response Remote response.
	 * @return int
	 */
	private static function get_response_code($response) {
		if (function_exists('wp_remote_retrieve_response_code')) {
			return (int) wp_remote_retrieve_response_code($response);
		}

		if (is_array($response) && isset($response['response']['code'])) {
			return (int) $response['response']['code'];
		}

		return 0;
	}

	/**
	 * Safely get response body from remote response payload.
	 *
	 * @param mixed $response Remote response.
	 * @return string
	 */
	private static function get_response_body($response) {
		if (function_exists('wp_remote_retrieve_body')) {
			$body = wp_remote_retrieve_body($response);
			return is_string($body) ? $body : '';
		}

		if (is_array($response) && isset($response['body']) && is_string($response['body'])) {
			return $response['body'];
		}

		return '';
	}

	/**
	 * Determine if value is WP_Error-like.
	 *
	 * @param mixed $value Value to inspect.
	 * @return bool
	 */
	private static function is_wp_error($value) {
		if (function_exists('is_wp_error')) {
			return is_wp_error($value);
		}

		return is_object($value) && class_exists('WP_Error') && $value instanceof WP_Error;
	}

	/**
	 * Extract readable error message.
	 *
	 * @param mixed $error Error payload.
	 * @return string
	 */
	private static function extract_error_message($error) {
		if (self::is_wp_error($error) && method_exists($error, 'get_error_message')) {
			$message = $error->get_error_message();
			if (is_string($message) && $message !== '') {
				return $message;
			}
		}

		if (is_string($error) && $error !== '') {
			return $error;
		}

		return __('Plugin update operation failed.', 'yt-consent-translations-1.3.5');
	}

	/**
	 * Join path fragments safely.
	 *
	 * @param string $base Base path.
	 * @param string $suffix Suffix path.
	 * @return string
	 */
	private static function join_path($base, $suffix) {
		$base = untrailingslashit((string) $base);
		$suffix = ltrim((string) $suffix, '/\\');
		return $base . '/' . $suffix;
	}

	/**
	 * Normalize path string for comparisons.
	 *
	 * @param string $path Path value.
	 * @return string
	 */
	private static function normalize_path($path) {
		$path = str_replace('\\', '/', (string) $path);
		return rtrim($path, '/');
	}
}
