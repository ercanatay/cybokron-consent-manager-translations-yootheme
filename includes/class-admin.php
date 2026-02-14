<?php
/**
 * Admin class - handles admin panel functionality.
 *
 * @package CYBOCOMA_Consent_Translations
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class CYBOCOMA_Admin
 */
class CYBOCOMA_Admin {

	/**
	 * Single instance.
	 *
	 * @var CYBOCOMA_Admin|null
	 */
	private static $instance = null;

	/**
	 * Get single instance.
	 *
	 * @return CYBOCOMA_Admin
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action('admin_menu', [$this, 'add_menu_page']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

		add_action('wp_ajax_cybocoma_save_settings', [$this, 'ajax_save_settings']);
		add_action('wp_ajax_cybocoma_reset_settings', [$this, 'ajax_reset_settings']);
		add_action('wp_ajax_cybocoma_export_settings', [$this, 'ajax_export_settings']);
		add_action('wp_ajax_cybocoma_import_settings', [$this, 'ajax_import_settings']);
		add_action('wp_ajax_cybocoma_load_language', [$this, 'ajax_load_language']);
		add_action('wp_ajax_cybocoma_load_scope', [$this, 'ajax_load_scope']);
		add_action('wp_ajax_cybocoma_get_snapshots', [$this, 'ajax_get_snapshots']);
		add_action('wp_ajax_cybocoma_restore_snapshot', [$this, 'ajax_restore_snapshot']);
		add_action('wp_ajax_cybocoma_health_check', [$this, 'ajax_health_check']);
		add_action('wp_ajax_cybocoma_quality_check', [$this, 'ajax_quality_check']);
		add_action('wp_ajax_cybocoma_check_update_now', [$this, 'ajax_check_update_now']);
		add_action('wp_ajax_cybocoma_copy_locale', [$this, 'ajax_copy_locale']);
	}

	/**
	 * Add menu page.
	 *
	 * @return void
	 */
	public function add_menu_page() {
		add_options_page(
			__('Cybokron Consent Manager Translations for YOOtheme Pro', 'cybokron-consent-manager-translations-yootheme'),
			__('Cybokron Consent Manager Translations for YOOtheme Pro', 'cybokron-consent-manager-translations-yootheme'),
			'manage_options',
			'cybokron-consent-manager-translations-yootheme',
			[$this, 'render_settings_page']
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets($hook) {
		if ($hook !== 'settings_page_cybokron-consent-manager-translations-yootheme') {
			return;
		}

		wp_enqueue_style(
			'cybocoma-admin-style',
			CYBOCOMA_PLUGIN_URL . 'admin/css/admin-style.css',
			[],
			CYBOCOMA_VERSION
		);

		wp_enqueue_script(
			'cybocoma-admin-script',
			CYBOCOMA_PLUGIN_URL . 'admin/js/admin-script.js',
			['jquery'],
			CYBOCOMA_VERSION,
			true
		);

		wp_localize_script('cybocoma-admin-script', 'cybocomaAdmin', [
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('cybocoma_admin_nonce'),
			'strings' => [
				'saving' => __('Saving...', 'cybokron-consent-manager-translations-yootheme'),
				'saved' => __('Settings saved successfully!', 'cybokron-consent-manager-translations-yootheme'),
				'error' => __('An error occurred. Please try again.', 'cybokron-consent-manager-translations-yootheme'),
				'confirmReset' => __('Are you sure you want to reset all settings for this locale scope to defaults?', 'cybokron-consent-manager-translations-yootheme'),
				'resetting' => __('Resetting...', 'cybokron-consent-manager-translations-yootheme'),
				'resetSuccess' => __('Settings reset successfully!', 'cybokron-consent-manager-translations-yootheme'),
				'importing' => __('Importing...', 'cybokron-consent-manager-translations-yootheme'),
				'importSuccess' => __('Settings imported successfully!', 'cybokron-consent-manager-translations-yootheme'),
				'invalidFile' => __('Please select a valid JSON file.', 'cybokron-consent-manager-translations-yootheme'),
				'languageLoaded' => __('Language preset loaded!', 'cybokron-consent-manager-translations-yootheme'),
				'scopeLoaded' => __('Locale scope loaded.', 'cybokron-consent-manager-translations-yootheme'),
				'qualityCheckRunning' => __('Running quality checks...', 'cybokron-consent-manager-translations-yootheme'),
				'qualityCheckOk' => __('No blocking quality issues found.', 'cybokron-consent-manager-translations-yootheme'),
				'healthCheckRunning' => __('Running compatibility health check...', 'cybokron-consent-manager-translations-yootheme'),
				'healthCheckOk' => __('Compatibility check completed.', 'cybokron-consent-manager-translations-yootheme'),
				'restored' => __('Snapshot restored successfully.', 'cybokron-consent-manager-translations-yootheme'),
				'unsavedChanges' => __('You have unsaved changes. Leave without saving?', 'cybokron-consent-manager-translations-yootheme'),
				'selectSnapshot' => __('Select a snapshot', 'cybokron-consent-manager-translations-yootheme'),
				'selectSnapshotFirst' => __('Select a snapshot first.', 'cybokron-consent-manager-translations-yootheme'),
				'qualityCheckFailed' => __('Quality check reported issues/warnings.', 'cybokron-consent-manager-translations-yootheme'),
				'checkUpdateRunning' => __('Checking WordPress.org update metadata...', 'cybokron-consent-manager-translations-yootheme'),
				'checkUpdateNoChange' => __('No new version found. Plugin is up to date.', 'cybokron-consent-manager-translations-yootheme'),
				'checkUpdateFound' => __('A new version is available. Update it from the Plugins screen.', 'cybokron-consent-manager-translations-yootheme'),
				'checkUpdateInstalled' => __('Plugin updates are managed by WordPress.', 'cybokron-consent-manager-translations-yootheme'),
				'checkUpdateInstallFailed' => __('Update check completed with an error. Check updater status.', 'cybokron-consent-manager-translations-yootheme'),
				'copyLocaleRunning' => __('Copying...', 'cybokron-consent-manager-translations-yootheme'),
				'confirmCopyLocale' => __('Copy all settings from the selected locale? This will overwrite current settings for this scope.', 'cybokron-consent-manager-translations-yootheme'),
				'selectSourceLocale' => __('Select a source locale first.', 'cybokron-consent-manager-translations-yootheme'),
				'statsSummary' => __('{customized}/{total} customized ({percent}%)', 'cybokron-consent-manager-translations-yootheme')
			]
		]);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if (!current_user_can('manage_options')) {
			return;
		}

		include CYBOCOMA_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Read a scalar POST field and sanitize it.
	 *
	 * @param string $key POST field name.
	 * @param string $default Default value when missing/invalid.
	 * @return string
	 */
	private function get_post_scalar($key, $default = '') {
		$value = filter_input(INPUT_POST, $key, FILTER_DEFAULT);
		if (!is_scalar($value)) {
			return $default;
		}

		return sanitize_text_field(wp_unslash((string) $value));
	}

	/**
	 * Verify nonce and capabilities for AJAX actions.
	 *
	 * @return void
	 */
	private function verify_ajax_request() {
		$this->send_ajax_security_headers();

		$nonce = $this->get_post_scalar('nonce');
		if (empty($nonce) || !wp_verify_nonce($nonce, 'cybocoma_admin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permission denied.', 'cybokron-consent-manager-translations-yootheme')]);
		}
	}

	/**
	 * Send security headers for admin AJAX responses.
	 *
	 * @return void
	 */
	protected function send_ajax_security_headers() {
		if ($this->headers_already_sent()) {
			return;
		}

		send_nosniff_header();
		if (!$this->has_response_header('X-Frame-Options')) {
			send_frame_options_header();
		}
	}

	/**
	 * Check whether headers have already been sent.
	 *
	 * @return bool
	 */
	protected function headers_already_sent() {
		return headers_sent();
	}

	/**
	 * Get currently queued response headers.
	 *
	 * @return array<int, string>
	 */
	protected function get_response_headers() {
		return headers_list();
	}

	/**
	 * Check whether a response header has already been set.
	 *
	 * @param string $header_name Header name without colon.
	 * @return bool
	 */
	private function has_response_header($header_name) {
		foreach ($this->get_response_headers() as $header) {
			if (stripos($header, $header_name . ':') === 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Sanitize consent string - allows only safe anchor tags.
	 *
	 * @param string $value Input string.
	 * @return string
	 */
	private function sanitize_consent_string($value) {
		static $allowed_html = [
			'a' => [
				'href' => true,
				'title' => true,
				'target' => true,
				'rel' => true
			]
		];
		$value = wp_kses($value, $allowed_html);

		return $value;
	}
	/**
	 * Validate selected language against supported list.
	 *
	 * @param string $language Selected language code.
	 * @return string
	 */
	private function get_valid_language($language) {
		return CYBOCOMA_Strings::is_valid_language($language) ? $language : 'en';
	}

	/**
	 * Get target locale scope from request.
	 *
	 * @param string|null $raw_locale Locale value.
	 * @return string
	 */
	private function get_scope_locale($raw_locale = null) {
		if ($raw_locale === null) {
			$raw_locale = $this->get_post_scalar('settings_locale');
		}

		return CYBOCOMA_Options::normalize_locale((string) $raw_locale);
	}

	/**
	 * Resolve actual language code considering locale-scope auto mode.
	 *
	 * @param string $language Language code.
	 * @param string $scope_locale WordPress locale scope.
	 * @return string
	 */
	private function resolve_language_for_scope($language, $scope_locale) {
		if ($language === 'auto') {
			return CYBOCOMA_Strings::detect_language_from_locale($scope_locale);
		}

		return CYBOCOMA_Strings::resolve_language_code($language, false);
	}

	/**
	 * Check whether a value contains required policy URL placeholder.
	 *
	 * @param string $value String value.
	 * @return bool
	 */
	private function has_policy_url_placeholder($value) {
		return strpos($value, '%s') !== false || strpos($value, '%1$s') !== false;
	}

	/**
	 * Get labels for fields missing required placeholders.
	 *
	 * @param array $strings Sanitized string values by key.
	 * @return array
	 */
	private function get_invalid_placeholder_fields($strings) {
		$invalid_fields = [];
		$placeholder_keys = ['banner_link', 'modal_content_link'];

		foreach ($placeholder_keys as $key) {
			if (isset($strings[$key]) && $strings[$key] !== '' && !$this->has_policy_url_placeholder($strings[$key])) {
				$invalid_fields[] = CYBOCOMA_Strings::get_key_label($key);
			}
		}

		return $invalid_fields;
	}

	/**
	 * Build diff-based custom string set against preset values.
	 *
	 * @param array $strings Submitted sanitized strings.
	 * @param array $preset_translations Preset translations for selected language.
	 * @return array
	 */
	private function build_custom_string_diff($strings, $preset_translations) {
		$custom_strings = [];
		$string_keys = array_keys(CYBOCOMA_Strings::get_string_keys());

		foreach ($string_keys as $key) {
			if (!isset($strings[$key])) {
				continue;
			}

			$value = (string) $strings[$key];
			$preset_value = isset($preset_translations[$key]) ? (string) $preset_translations[$key] : '';

			if ($value !== '' && $value !== $preset_value) {
				$custom_strings[$key] = $value;
			}
		}

		return $custom_strings;
	}

	/**
	 * Parse and sanitize string payload from request.
	 *
	 * @return array
	 */
	private function parse_submitted_strings() {
		$submitted_strings = [];
		$string_keys = array_keys(CYBOCOMA_Strings::get_string_keys());
		$raw_strings = filter_input(INPUT_POST, 'strings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

		if (!is_array($raw_strings)) {
			return $submitted_strings;
		}

		foreach ($string_keys as $key) {
			if (!isset($raw_strings[$key])) {
				continue;
			}

			$raw_value = wp_unslash($raw_strings[$key]);
			if (!is_scalar($raw_value)) {
				continue;
			}

			$value = $this->sanitize_consent_string((string) $raw_value);
			$submitted_strings[$key] = $value;
		}

		return $submitted_strings;
	}

	/**
	 * Build quality report for UI checks.
	 *
	 * @param array $strings Effective string values.
	 * @param array $preset_translations Preset values.
	 * @return array
	 */
	private function build_quality_report($strings, $preset_translations) {
		$issues = [];
		$warnings = [];

		$invalid_placeholder_fields = $this->get_invalid_placeholder_fields($strings);
		if (!empty($invalid_placeholder_fields)) {
			$issues[] = sprintf(
				/* translators: 1: %s placeholder, 2: %1$s placeholder, 3: field labels. */
				__('The following fields must include %1$s or %2$s: %3$s', 'cybokron-consent-manager-translations-yootheme'),
				'%s',
				'%1$s',
				implode(', ', $invalid_placeholder_fields)
			);
		}

		foreach ($strings as $key => $value) {
			$value = is_scalar($value) ? (string) $value : '';
			$reference = isset($preset_translations[$key]) ? (string) $preset_translations[$key] : '';

			if ($value !== '' && substr_count($value, '<a ') !== substr_count($value, '</a>')) {
				$warnings[] = sprintf(
					/* translators: %s field label */
					__('%s may contain malformed anchor HTML.', 'cybokron-consent-manager-translations-yootheme'),
					CYBOCOMA_Strings::get_key_label($key)
				);
			}

			if ($value !== '' && $reference !== '' && strlen($reference) > 40) {
				$ratio = strlen($value) / strlen($reference);
				if ($ratio > 1.8) {
					$warnings[] = sprintf(
						/* translators: %s field label */
						__('%s is much longer than the preset and may overflow on small screens.', 'cybokron-consent-manager-translations-yootheme'),
						CYBOCOMA_Strings::get_key_label($key)
					);
				}
			}
		}

		$pairs = [
			['button_accept', 'button_reject'],
			['modal_accept', 'modal_reject'],
			['show_services', 'hide_services']
		];

		foreach ($pairs as $pair) {
			$left = isset($strings[$pair[0]]) ? trim((string) $strings[$pair[0]]) : '';
			$right = isset($strings[$pair[1]]) ? trim((string) $strings[$pair[1]]) : '';
			$left_cmp = function_exists('mb_strtolower') ? mb_strtolower($left) : strtolower($left);
			$right_cmp = function_exists('mb_strtolower') ? mb_strtolower($right) : strtolower($right);
			if ($left !== '' && $right !== '' && $left_cmp === $right_cmp) {
				$warnings[] = sprintf(
					/* translators: 1: first field label, 2: second field label */
					__('Fields %1$s and %2$s are identical. Consider using distinct labels for clarity.', 'cybokron-consent-manager-translations-yootheme'),
					CYBOCOMA_Strings::get_key_label($pair[0]),
					CYBOCOMA_Strings::get_key_label($pair[1])
				);
			}
		}

		return [
			'status' => !empty($issues) ? 'error' : (!empty($warnings) ? 'warning' : 'ok'),
			'issues' => array_values(array_unique($issues)),
			'warnings' => array_values(array_unique($warnings))
		];
	}

	/**
	 * Build current locale-scope payload used by admin UI.
	 *
	 * @param string $scope_locale Locale scope.
	 * @return array
	 */
	private function build_scope_payload($scope_locale) {
		$scope_locale = $this->get_scope_locale($scope_locale);
		$options = CYBOCOMA_Options::get_options($scope_locale);
		$language = isset($options['language']) ? $this->get_valid_language($options['language']) : 'en';
		$resolved_language = $this->resolve_language_for_scope($language, $scope_locale);
		$preset_translations = CYBOCOMA_Strings::get_translations($resolved_language);
		$custom_strings = isset($options['custom_strings']) && is_array($options['custom_strings']) ? $options['custom_strings'] : [];

		$effective_strings = $preset_translations;
		foreach ($custom_strings as $key => $value) {
			if ($value !== '') {
				$effective_strings[$key] = $value;
			}
		}

		$quality = $this->build_quality_report($effective_strings, $preset_translations);
		$health = CYBOCOMA_Health::build_summary(isset($options['enabled']) ? (bool) $options['enabled'] : true);
		$snapshots = CYBOCOMA_Options::get_snapshots($scope_locale);

		$snapshot_summaries = [];
		foreach ($snapshots as $snapshot) {
			$snapshot_summaries[] = [
				'id' => $snapshot['id'],
				'label' => $snapshot['label'],
				'created_at' => $snapshot['created_at']
			];
		}

		return [
			'scopeLocale' => $scope_locale,
			'options' => $options,
			'language' => $language,
			'resolvedLanguage' => $resolved_language,
			'presetTranslations' => $preset_translations,
			'effectiveStrings' => $effective_strings,
			'quality' => $quality,
			'health' => $health,
			'snapshots' => $snapshot_summaries,
			'updater' => CYBOCOMA_Updater::get_admin_payload()
		];
	}

	/**
	 * AJAX: Save settings.
	 *
	 * @return void
	 */
	public function ajax_save_settings() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$enabled = $this->get_post_scalar('enabled', '0') !== '0';
		$update_channel_enabled = $this->get_post_scalar('update_channel_enabled', '0') !== '0';
		$language = $this->get_post_scalar('language', 'en');
		$language = $this->get_valid_language($language);

		$resolved_language = $this->resolve_language_for_scope($language, $scope_locale);
		$preset_translations = CYBOCOMA_Strings::get_translations($resolved_language);
		$submitted_strings = $this->parse_submitted_strings();

		$invalid_placeholder_fields = $this->get_invalid_placeholder_fields($submitted_strings);
		if (!empty($invalid_placeholder_fields)) {
			$message = sprintf(
				/* translators: 1: %s placeholder, 2: %1$s placeholder, 3: field labels. */
				__('The following fields must include %1$s or %2$s: %3$s', 'cybokron-consent-manager-translations-yootheme'),
				'%s',
				'%1$s',
				implode(', ', $invalid_placeholder_fields)
			);
			wp_send_json_error(['message' => $message]);
		}

		$custom_strings = $this->build_custom_string_diff($submitted_strings, $preset_translations);
		$options = [
			'enabled' => $enabled,
			'language' => $language,
			'custom_strings' => $custom_strings
		];

		$stored = CYBOCOMA_Options::update_options($options, $scope_locale, 'manual_save');
		CYBOCOMA_Updater::update_settings([
			'enabled' => $update_channel_enabled
		]);

		CYBOCOMA_Translator::get_instance()->clear_cache();
		CYBOCOMA_Strings::clear_cache();

		$scope_payload = $this->build_scope_payload($scope_locale);
		$scope_payload['options'] = $stored;

		wp_send_json_success([
			'message' => __('Settings saved successfully!', 'cybokron-consent-manager-translations-yootheme'),
			'scope' => $scope_payload
		]);
	}

	/**
	 * AJAX: Reset settings for current locale.
	 *
	 * @return void
	 */
	public function ajax_reset_settings() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$defaults = CYBOCOMA_Options::get_default_options();
		$stored = CYBOCOMA_Options::update_options($defaults, $scope_locale, 'reset_default');

		CYBOCOMA_Translator::get_instance()->clear_cache();
		CYBOCOMA_Strings::clear_cache();

		wp_send_json_success([
			'message' => __('Settings reset successfully!', 'cybokron-consent-manager-translations-yootheme'),
			'scope' => $this->build_scope_payload($scope_locale),
			'options' => $stored
		]);
	}

	/**
	 * AJAX: Export settings.
	 *
	 * @return void
	 */
	public function ajax_export_settings() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$options = CYBOCOMA_Options::get_options($scope_locale);
		$all_locale_options = CYBOCOMA_Options::get_all_locale_options();

		$data = [
			'version' => CYBOCOMA_VERSION,
			'exported_at' => gmdate('c'),
			'scope_locale' => $scope_locale,
			'options' => $options,
			'by_locale' => $all_locale_options
		];

		$filename = sprintf('cybokron-consent-manager-translations-yootheme-%s-%s.json', strtolower($scope_locale), gmdate('Ymd-His'));

		wp_send_json_success([
			'filename' => $filename,
			'data' => $data
		]);
	}

	/**
	 * AJAX: Import settings.
	 *
	 * @return void
	 */
	public function ajax_import_settings() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce already validated in verify_ajax_request().
		if (!isset($_FILES['import_file'])) {
			wp_send_json_error(['message' => __('No file uploaded.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is validated and file payload is validated below.
		$cybocoma_file = $_FILES['import_file'];

		if (!isset($cybocoma_file['error']) || $cybocoma_file['error'] !== UPLOAD_ERR_OK) {
			wp_send_json_error(['message' => __('File upload failed.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		$max_size = 150 * 1024;
		if (!isset($cybocoma_file['size']) || $cybocoma_file['size'] > $max_size) {
			wp_send_json_error(['message' => __('File too large. Maximum size is 150KB.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		if (!isset($cybocoma_file['name'])) {
			wp_send_json_error(['message' => __('Invalid file.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		$file_info = wp_check_filetype(sanitize_file_name($cybocoma_file['name']));
		static $allowed_extensions = ['json' => true];
		if (!$file_info['ext'] || !isset($allowed_extensions[strtolower($file_info['ext'])])) {
			wp_send_json_error(['message' => __('Invalid file type. Only JSON files are allowed.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		if (!isset($cybocoma_file['tmp_name']) || !is_uploaded_file($cybocoma_file['tmp_name'])) {
			wp_send_json_error(['message' => __('Invalid file upload.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading uploaded temp file
		$content = file_get_contents($cybocoma_file['tmp_name']);
		$data = json_decode($content, true);

		if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
			wp_send_json_error(['message' => __('Invalid JSON file.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		$import_payload = null;
		if (isset($data['by_locale']) && is_array($data['by_locale'])) {
			$scope_key = strtoupper(str_replace('_', '-', $scope_locale));
			if (isset($data['by_locale'][$scope_key]) && is_array($data['by_locale'][$scope_key])) {
				$import_payload = $data['by_locale'][$scope_key];
			}
		}

		if ($import_payload === null && isset($data['options']) && is_array($data['options'])) {
			$import_payload = $data['options'];
		}

		if ($import_payload === null) {
			$import_payload = $data;
		}

		$enabled = isset($import_payload['enabled']) ? (bool) $import_payload['enabled'] : true;
		$language = isset($import_payload['language']) && is_scalar($import_payload['language']) ? sanitize_text_field((string) $import_payload['language']) : 'en';
		$language = $this->get_valid_language($language);

		$resolved_language = $this->resolve_language_for_scope($language, $scope_locale);
		$preset_translations = CYBOCOMA_Strings::get_translations($resolved_language);

		$submitted_strings = [];
		if (isset($import_payload['custom_strings']) && is_array($import_payload['custom_strings'])) {
			$string_keys = CYBOCOMA_Strings::get_string_keys();
			foreach ($import_payload['custom_strings'] as $key => $value) {
				if (isset($string_keys[$key]) && is_scalar($value)) {
					$submitted_strings[$key] = $this->sanitize_consent_string((string) $value);
				}
			}
		}

		$invalid_placeholder_fields = $this->get_invalid_placeholder_fields($submitted_strings);
		if (!empty($invalid_placeholder_fields)) {
			$message = sprintf(
				/* translators: 1: %s placeholder, 2: %1$s placeholder, 3: field labels. */
				__('The following fields must include %1$s or %2$s: %3$s', 'cybokron-consent-manager-translations-yootheme'),
				'%s',
				'%1$s',
				implode(', ', $invalid_placeholder_fields)
			);
			wp_send_json_error(['message' => $message]);
		}

		$options = [
			'enabled' => $enabled,
			'language' => $language,
			'custom_strings' => $this->build_custom_string_diff($submitted_strings, $preset_translations)
		];

		$stored = CYBOCOMA_Options::update_options($options, $scope_locale, 'import');

		CYBOCOMA_Translator::get_instance()->clear_cache();
		CYBOCOMA_Strings::clear_cache();

		wp_send_json_success([
			'message' => __('Settings imported successfully!', 'cybokron-consent-manager-translations-yootheme'),
			'scope' => $this->build_scope_payload($scope_locale),
			'options' => $stored
		]);
	}

	/**
	 * AJAX: Load language preset.
	 *
	 * @return void
	 */
	public function ajax_load_language() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$language = $this->get_post_scalar('language', 'en');
		$language = $this->get_valid_language($language);
		$resolved_language = $this->resolve_language_for_scope($language, $scope_locale);
		$translations = CYBOCOMA_Strings::get_translations($resolved_language);

		wp_send_json_success([
			'language' => $language,
			'resolvedLanguage' => $resolved_language,
			'translations' => $translations,
			'scopeLocale' => $scope_locale
		]);
	}

	/**
	 * AJAX: Load settings and state for locale scope.
	 *
	 * @return void
	 */
	public function ajax_load_scope() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		wp_send_json_success([
			'scope' => $this->build_scope_payload($scope_locale)
		]);
	}

	/**
	 * AJAX: Get snapshots for locale.
	 *
	 * @return void
	 */
	public function ajax_get_snapshots() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$snapshots = CYBOCOMA_Options::get_snapshots($scope_locale);
		$summary = [];
		foreach ($snapshots as $snapshot) {
			$summary[] = [
				'id' => $snapshot['id'],
				'label' => $snapshot['label'],
				'created_at' => $snapshot['created_at']
			];
		}

		wp_send_json_success(['snapshots' => $summary]);
	}

	/**
	 * AJAX: Restore snapshot for locale.
	 *
	 * @return void
	 */
	public function ajax_restore_snapshot() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$snapshot_id = $this->get_post_scalar('snapshot_id');
		if ($snapshot_id === '') {
			wp_send_json_error(['message' => __('Snapshot ID is required.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		$restored = CYBOCOMA_Options::restore_snapshot($snapshot_id, $scope_locale);
		if (!is_array($restored)) {
			wp_send_json_error(['message' => __('Snapshot could not be restored.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		CYBOCOMA_Translator::get_instance()->clear_cache();
		CYBOCOMA_Strings::clear_cache();

		wp_send_json_success([
			'message' => __('Snapshot restored successfully.', 'cybokron-consent-manager-translations-yootheme'),
			'scope' => $this->build_scope_payload($scope_locale),
			'options' => $restored
		]);
	}

	/**
	 * AJAX: Run compatibility health check.
	 *
	 * @return void
	 */
	public function ajax_health_check() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$options = CYBOCOMA_Options::get_options($scope_locale);
		$summary = CYBOCOMA_Health::build_summary(isset($options['enabled']) ? (bool) $options['enabled'] : true);

		wp_send_json_success([
			'health' => $summary
		]);
	}

	/**
	 * AJAX: Run quality checks with current strings.
	 *
	 * @return void
	 */
	public function ajax_quality_check() {
		$this->verify_ajax_request();

		$scope_locale = $this->get_scope_locale();
		$language = $this->get_post_scalar('language', 'en');
		$language = $this->get_valid_language($language);

		$resolved_language = $this->resolve_language_for_scope($language, $scope_locale);
		$preset_translations = CYBOCOMA_Strings::get_translations($resolved_language);
		$submitted_strings = $this->parse_submitted_strings();

		$effective_strings = $preset_translations;
		foreach ($submitted_strings as $key => $value) {
			if ($value !== '') {
				$effective_strings[$key] = $value;
			}
		}

		$quality = $this->build_quality_report($effective_strings, $preset_translations);
		wp_send_json_success([
			'quality' => $quality
		]);
	}

	/**
	 * AJAX: Run immediate WordPress.org update check.
	 *
	 * @return void
	 */
	public function ajax_check_update_now() {
		$this->verify_ajax_request();

		$updater = CYBOCOMA_Updater::manual_check();

		$message = __('No new version found. Plugin is up to date.', 'cybokron-consent-manager-translations-yootheme');
		if (!empty($updater['status']) && $updater['status'] === 'error') {
			$message = __('Update check completed with an error. Check updater status.', 'cybokron-consent-manager-translations-yootheme');
		} elseif (!empty($updater['updateAvailable'])) {
			$message = __('A new version is available. Update it from the Plugins screen.', 'cybokron-consent-manager-translations-yootheme');
		}

		wp_send_json_success([
			'message' => $message,
			'updater' => CYBOCOMA_Updater::get_admin_payload()
		]);
	}

	/**
	 * AJAX: Copy settings from one locale scope to another.
	 *
	 * @return void
	 */
	public function ajax_copy_locale() {
		$this->verify_ajax_request();

		$source_locale = $this->get_scope_locale($this->get_post_scalar('source_locale'));
		$target_locale = $this->get_scope_locale();

		if ($source_locale === $target_locale) {
			wp_send_json_error(['message' => __('Source and target locales are the same.', 'cybokron-consent-manager-translations-yootheme')]);
		}

		$source_options = CYBOCOMA_Options::get_options($source_locale);
		$stored = CYBOCOMA_Options::update_options($source_options, $target_locale, 'copy_locale');

		CYBOCOMA_Translator::get_instance()->clear_cache();
		CYBOCOMA_Strings::clear_cache();

		wp_send_json_success([
			'message' => sprintf(
				/* translators: %s source locale code */
				__('Settings copied from %s successfully!', 'cybokron-consent-manager-translations-yootheme'),
				$source_locale
			),
			'scope' => $this->build_scope_payload($target_locale)
		]);
	}
}
