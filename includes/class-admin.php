<?php
/**
 * Admin class - handles admin panel functionality.
 *
 * @package YT_Consent_Translations
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class YTCT_Admin
 */
class YTCT_Admin {

	/**
	 * Single instance.
	 *
	 * @var YTCT_Admin|null
	 */
	private static $instance = null;

	/**
	 * Get single instance.
	 *
	 * @return YTCT_Admin
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

		add_action('wp_ajax_ytct_save_settings', [$this, 'ajax_save_settings']);
		add_action('wp_ajax_ytct_reset_settings', [$this, 'ajax_reset_settings']);
		add_action('wp_ajax_ytct_export_settings', [$this, 'ajax_export_settings']);
		add_action('wp_ajax_ytct_import_settings', [$this, 'ajax_import_settings']);
		add_action('wp_ajax_ytct_load_language', [$this, 'ajax_load_language']);
		add_action('wp_ajax_ytct_load_scope', [$this, 'ajax_load_scope']);
		add_action('wp_ajax_ytct_get_snapshots', [$this, 'ajax_get_snapshots']);
		add_action('wp_ajax_ytct_restore_snapshot', [$this, 'ajax_restore_snapshot']);
		add_action('wp_ajax_ytct_health_check', [$this, 'ajax_health_check']);
		add_action('wp_ajax_ytct_quality_check', [$this, 'ajax_quality_check']);
		add_action('wp_ajax_ytct_check_update_now', [$this, 'ajax_check_update_now']);
	}

	/**
	 * Add menu page.
	 *
	 * @return void
	 */
	public function add_menu_page() {
		add_options_page(
			__('YT Consent Translations', 'yt-consent-translations-1.3.5'),
			__('YT Consent Translations', 'yt-consent-translations-1.3.5'),
			'manage_options',
			'yt-consent-translations',
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
		if ($hook !== 'settings_page_yt-consent-translations') {
			return;
		}

		wp_enqueue_style(
			'ytct-admin-style',
			YTCT_PLUGIN_URL . 'admin/css/admin-style.css',
			[],
			YTCT_VERSION
		);

		wp_enqueue_script(
			'ytct-admin-script',
			YTCT_PLUGIN_URL . 'admin/js/admin-script.js',
			['jquery'],
			YTCT_VERSION,
			true
		);

		wp_localize_script('ytct-admin-script', 'ytctAdmin', [
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ytct_admin_nonce'),
			'strings' => [
				'saving' => __('Saving...', 'yt-consent-translations-1.3.5'),
				'saved' => __('Settings saved successfully!', 'yt-consent-translations-1.3.5'),
				'error' => __('An error occurred. Please try again.', 'yt-consent-translations-1.3.5'),
				'confirmReset' => __('Are you sure you want to reset all settings for this locale scope to defaults?', 'yt-consent-translations-1.3.5'),
				'resetting' => __('Resetting...', 'yt-consent-translations-1.3.5'),
				'resetSuccess' => __('Settings reset successfully!', 'yt-consent-translations-1.3.5'),
				'importing' => __('Importing...', 'yt-consent-translations-1.3.5'),
				'importSuccess' => __('Settings imported successfully!', 'yt-consent-translations-1.3.5'),
				'invalidFile' => __('Please select a valid JSON file.', 'yt-consent-translations-1.3.5'),
				'languageLoaded' => __('Language preset loaded!', 'yt-consent-translations-1.3.5'),
				'scopeLoaded' => __('Locale scope loaded.', 'yt-consent-translations-1.3.5'),
				'qualityCheckRunning' => __('Running quality checks...', 'yt-consent-translations-1.3.5'),
				'qualityCheckOk' => __('No blocking quality issues found.', 'yt-consent-translations-1.3.5'),
				'healthCheckRunning' => __('Running compatibility health check...', 'yt-consent-translations-1.3.5'),
				'healthCheckOk' => __('Compatibility check completed.', 'yt-consent-translations-1.3.5'),
				'restored' => __('Snapshot restored successfully.', 'yt-consent-translations-1.3.5'),
				'unsavedChanges' => __('You have unsaved changes. Leave without saving?', 'yt-consent-translations-1.3.5'),
				'selectSnapshot' => __('Select a snapshot', 'yt-consent-translations-1.3.5'),
				'selectSnapshotFirst' => __('Select a snapshot first.', 'yt-consent-translations-1.3.5'),
				'qualityCheckFailed' => __('Quality check reported issues/warnings.', 'yt-consent-translations-1.3.5'),
				'checkUpdateRunning' => __('Checking GitHub stable release...', 'yt-consent-translations-1.3.5'),
				'checkUpdateNoChange' => __('No new version found. Plugin is up to date.', 'yt-consent-translations-1.3.5'),
				'checkUpdateFound' => __('New version detected. Enable update channel to allow auto-install.', 'yt-consent-translations-1.3.5'),
				'checkUpdateInstalled' => __('New version installed successfully.', 'yt-consent-translations-1.3.5'),
				'checkUpdateInstallFailed' => __('New version detected, but installation failed. Check updater status.', 'yt-consent-translations-1.3.5')
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

		include YTCT_PLUGIN_DIR . 'admin/views/settings-page.php';
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
		if (!headers_sent()) {
			header('X-Content-Type-Options: nosniff');
			header('X-Frame-Options: SAMEORIGIN');
		}

		$nonce = $this->get_post_scalar('nonce');
		if (empty($nonce) || !wp_verify_nonce($nonce, 'ytct_admin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations-1.3.5')]);
		}

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations-1.3.5')]);
		}
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
		return wp_kses($value, $allowed_html);
	}

	/**
	 * Validate selected language against supported list.
	 *
	 * @param string $language Selected language code.
	 * @return string
	 */
	private function get_valid_language($language) {
		return YTCT_Strings::is_valid_language($language) ? $language : 'en';
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

		return YTCT_Options::normalize_locale((string) $raw_locale);
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
			return YTCT_Strings::detect_language_from_locale($scope_locale);
		}

		return YTCT_Strings::resolve_language_code($language, false);
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
				$invalid_fields[] = YTCT_Strings::get_key_label($key);
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
		$string_keys = array_keys(YTCT_Strings::get_string_keys());

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
		$string_keys = array_keys(YTCT_Strings::get_string_keys());
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
				__('The following fields must include %1$s or %2$s: %3$s', 'yt-consent-translations-1.3.5'),
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
					__('%s may contain malformed anchor HTML.', 'yt-consent-translations-1.3.5'),
					YTCT_Strings::get_key_label($key)
				);
			}

			if ($value !== '' && $reference !== '' && strlen($reference) > 40) {
				$ratio = strlen($value) / strlen($reference);
				if ($ratio > 1.8) {
					$warnings[] = sprintf(
						/* translators: %s field label */
						__('%s is much longer than the preset and may overflow on small screens.', 'yt-consent-translations-1.3.5'),
						YTCT_Strings::get_key_label($key)
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
					__('Fields %1$s and %2$s are identical. Consider using distinct labels for clarity.', 'yt-consent-translations-1.3.5'),
					YTCT_Strings::get_key_label($pair[0]),
					YTCT_Strings::get_key_label($pair[1])
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
		$options = YTCT_Options::get_options($scope_locale);
		$language = isset($options['language']) ? $this->get_valid_language($options['language']) : 'en';
		$resolved_language = $this->resolve_language_for_scope($language, $scope_locale);
		$preset_translations = YTCT_Strings::get_translations($resolved_language);
		$custom_strings = isset($options['custom_strings']) && is_array($options['custom_strings']) ? $options['custom_strings'] : [];

		$effective_strings = $preset_translations;
		foreach ($custom_strings as $key => $value) {
			if ($value !== '') {
				$effective_strings[$key] = $value;
			}
		}

		$quality = $this->build_quality_report($effective_strings, $preset_translations);
		$health = YTCT_Health::build_summary(isset($options['enabled']) ? (bool) $options['enabled'] : true);
		$snapshots = YTCT_Options::get_snapshots($scope_locale);

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
			'updater' => YTCT_Updater::get_admin_payload()
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
		$preset_translations = YTCT_Strings::get_translations($resolved_language);
		$submitted_strings = $this->parse_submitted_strings();

		$invalid_placeholder_fields = $this->get_invalid_placeholder_fields($submitted_strings);
		if (!empty($invalid_placeholder_fields)) {
			$message = sprintf(
				/* translators: 1: %s placeholder, 2: %1$s placeholder, 3: field labels. */
				__('The following fields must include %1$s or %2$s: %3$s', 'yt-consent-translations-1.3.5'),
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

		$stored = YTCT_Options::update_options($options, $scope_locale, 'manual_save');
		YTCT_Updater::update_settings([
			'enabled' => $update_channel_enabled
		]);

		YTCT_Translator::get_instance()->clear_cache();
		YTCT_Strings::clear_cache();

		$scope_payload = $this->build_scope_payload($scope_locale);
		$scope_payload['options'] = $stored;

		wp_send_json_success([
			'message' => __('Settings saved successfully!', 'yt-consent-translations-1.3.5'),
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
		$defaults = YTCT_Options::get_default_options();
		$stored = YTCT_Options::update_options($defaults, $scope_locale, 'reset_default');

		YTCT_Translator::get_instance()->clear_cache();
		YTCT_Strings::clear_cache();

		wp_send_json_success([
			'message' => __('Settings reset successfully!', 'yt-consent-translations-1.3.5'),
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
		$options = YTCT_Options::get_options($scope_locale);
		$all_locale_options = YTCT_Options::get_all_locale_options();

		$data = [
			'version' => YTCT_VERSION,
			'exported_at' => gmdate('c'),
			'scope_locale' => $scope_locale,
			'options' => $options,
			'by_locale' => $all_locale_options
		];

		$filename = sprintf('yt-consent-translations-%s-%s.json', strtolower($scope_locale), gmdate('Ymd-His'));

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
			wp_send_json_error(['message' => __('No file uploaded.', 'yt-consent-translations-1.3.5')]);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce is validated and file payload is validated below.
		$ytct_file = $_FILES['import_file'];

		if (!isset($ytct_file['error']) || $ytct_file['error'] !== UPLOAD_ERR_OK) {
			wp_send_json_error(['message' => __('File upload failed.', 'yt-consent-translations-1.3.5')]);
		}

		$max_size = 150 * 1024;
		if (!isset($ytct_file['size']) || $ytct_file['size'] > $max_size) {
			wp_send_json_error(['message' => __('File too large. Maximum size is 150KB.', 'yt-consent-translations-1.3.5')]);
		}

		if (!isset($ytct_file['name'])) {
			wp_send_json_error(['message' => __('Invalid file.', 'yt-consent-translations-1.3.5')]);
		}

		$file_info = wp_check_filetype(sanitize_file_name($ytct_file['name']));
		static $allowed_extensions = ['json' => true];
		if (!$file_info['ext'] || !isset($allowed_extensions[strtolower($file_info['ext'])])) {
			wp_send_json_error(['message' => __('Invalid file type. Only JSON files are allowed.', 'yt-consent-translations-1.3.5')]);
		}

		if (!isset($ytct_file['tmp_name']) || !is_uploaded_file($ytct_file['tmp_name'])) {
			wp_send_json_error(['message' => __('Invalid file upload.', 'yt-consent-translations-1.3.5')]);
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading uploaded temp file
		$content = file_get_contents($ytct_file['tmp_name']);
		$data = json_decode($content, true);

		if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
			wp_send_json_error(['message' => __('Invalid JSON file.', 'yt-consent-translations-1.3.5')]);
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
		$preset_translations = YTCT_Strings::get_translations($resolved_language);

		$submitted_strings = [];
		if (isset($import_payload['custom_strings']) && is_array($import_payload['custom_strings'])) {
			$string_keys = YTCT_Strings::get_string_keys();
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
				__('The following fields must include %1$s or %2$s: %3$s', 'yt-consent-translations-1.3.5'),
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

		$stored = YTCT_Options::update_options($options, $scope_locale, 'import');

		YTCT_Translator::get_instance()->clear_cache();
		YTCT_Strings::clear_cache();

		wp_send_json_success([
			'message' => __('Settings imported successfully!', 'yt-consent-translations-1.3.5'),
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
		$translations = YTCT_Strings::get_translations($resolved_language);

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
		$snapshots = YTCT_Options::get_snapshots($scope_locale);
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
			wp_send_json_error(['message' => __('Snapshot ID is required.', 'yt-consent-translations-1.3.5')]);
		}

		$restored = YTCT_Options::restore_snapshot($snapshot_id, $scope_locale);
		if (!is_array($restored)) {
			wp_send_json_error(['message' => __('Snapshot could not be restored.', 'yt-consent-translations-1.3.5')]);
		}

		YTCT_Translator::get_instance()->clear_cache();
		YTCT_Strings::clear_cache();

		wp_send_json_success([
			'message' => __('Snapshot restored successfully.', 'yt-consent-translations-1.3.5'),
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
		$options = YTCT_Options::get_options($scope_locale);
		$summary = YTCT_Health::build_summary(isset($options['enabled']) ? (bool) $options['enabled'] : true);

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
		$preset_translations = YTCT_Strings::get_translations($resolved_language);
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
	 * AJAX: Run immediate GitHub update check.
	 *
	 * @return void
	 */
	public function ajax_check_update_now() {
		$this->verify_ajax_request();

		$updater = YTCT_Updater::manual_check();

		$message = __('No new version found. Plugin is up to date.', 'yt-consent-translations-1.3.5');
		if (!empty($updater['installationAttempted']) && !empty($updater['installationSucceeded'])) {
			$message = __('New version installed successfully.', 'yt-consent-translations-1.3.5');
		} elseif (!empty($updater['installationAttempted'])) {
			$message = __('New version detected, but installation failed. Check updater status.', 'yt-consent-translations-1.3.5');
		} elseif (!empty($updater['updateAvailable'])) {
			$message = __('New version detected. Enable update channel to allow auto-install.', 'yt-consent-translations-1.3.5');
		}

		wp_send_json_success([
			'message' => $message,
			'updater' => YTCT_Updater::get_admin_payload()
		]);
	}
}
