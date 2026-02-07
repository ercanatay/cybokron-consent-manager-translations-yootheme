<?php
/**
 * Admin class - handles admin panel functionality
 *
 * @package YT_Consent_Translations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class YTCT_Admin
 * Admin panel functionality
 */
class YTCT_Admin {

	/**
	 * Single instance
	 */
	private static $instance = null;

	/**
	 * Get single instance
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action('admin_menu', [$this, 'add_menu_page']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
		add_action('wp_ajax_ytct_save_settings', [$this, 'ajax_save_settings']);
		add_action('wp_ajax_ytct_reset_settings', [$this, 'ajax_reset_settings']);
		add_action('wp_ajax_ytct_export_settings', [$this, 'ajax_export_settings']);
		add_action('wp_ajax_ytct_import_settings', [$this, 'ajax_import_settings']);
		add_action('wp_ajax_ytct_load_language', [$this, 'ajax_load_language']);
	}

	/**
	 * Add menu page
	 */
	public function add_menu_page() {
		add_options_page(
			__('YT Consent Translations', 'yt-consent-translations'),
			__('YT Consent Translations', 'yt-consent-translations'),
			'manage_options',
			'yt-consent-translations',
			[$this, 'render_settings_page']
		);
	}

	/**
	 * Enqueue admin assets
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
				'saving' => __('Saving...', 'yt-consent-translations'),
				'saved' => __('Settings saved successfully!', 'yt-consent-translations'),
				'error' => __('An error occurred. Please try again.', 'yt-consent-translations'),
				'confirmReset' => __('Are you sure you want to reset all settings to default?', 'yt-consent-translations'),
				'resetting' => __('Resetting...', 'yt-consent-translations'),
				'resetSuccess' => __('Settings reset successfully!', 'yt-consent-translations'),
				'importing' => __('Importing...', 'yt-consent-translations'),
				'importSuccess' => __('Settings imported successfully!', 'yt-consent-translations'),
				'invalidFile' => __('Please select a valid JSON file.', 'yt-consent-translations'),
				'languageLoaded' => __('Language preset loaded!', 'yt-consent-translations')
			]
		]);
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		// Check permissions
		if (!current_user_can('manage_options')) {
			return;
		}

		include YTCT_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Sanitize consent string - allows only safe <a> tags
	 *
	 * @param string $value Input string
	 * @return string Sanitized string
	 */
	private function sanitize_consent_string($value) {
		// Only allow <a> tags with href attribute (for privacy policy links)
		$allowed_html = [
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
	 * Validate selected language against supported list
	 *
	 * @param string $language Selected language code
	 * @return string
	 */
	private function get_valid_language($language) {
		$valid_languages = array_keys(YTCT_Strings::get_languages());
		return in_array($language, $valid_languages, true) ? $language : 'en';
	}

	/**
	 * Check whether a value contains required policy URL placeholder
	 *
	 * @param string $value String value
	 * @return bool
	 */
	private function has_policy_url_placeholder($value) {
		return strpos($value, '%s') !== false || strpos($value, '%1$s') !== false;
	}

	/**
	 * Get labels for fields missing required placeholders
	 *
	 * @param array $strings Sanitized string values by key
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
	 * Build diff-based custom string set against preset values
	 *
	 * @param array $strings Submitted sanitized strings
	 * @param array $preset_translations Preset translations for selected language
	 * @return array
	 */
	private function build_custom_string_diff($strings, $preset_translations) {
		$custom_strings = [];
		$string_keys = array_keys(YTCT_Strings::get_string_keys());

		foreach ($string_keys as $key) {
			if (!isset($strings[$key]) || $strings[$key] === '') {
				continue;
			}

			$value = (string) $strings[$key];
			$preset_value = isset($preset_translations[$key]) ? (string) $preset_translations[$key] : '';

			if ($value !== $preset_value) {
				$custom_strings[$key] = $value;
			}
		}

		return $custom_strings;
	}

	/**
	 * AJAX: Save settings
	 */
	public function ajax_save_settings() {
		// Check nonce
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (empty($nonce) || !wp_verify_nonce($nonce, 'ytct_admin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
		}

		// Check permissions
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
		}

		// Get and sanitize data
		// Hidden input sends "0" when unchecked, checkbox sends "1" when checked
		$enabled = !empty($_POST['enabled']) && sanitize_text_field(wp_unslash($_POST['enabled'])) !== '0';
		$language = isset($_POST['language']) ? sanitize_text_field(wp_unslash($_POST['language'])) : 'en';
		$language = $this->get_valid_language($language);

		$resolved_language = YTCT_Strings::resolve_language_code($language, true);
		$preset_translations = YTCT_Strings::get_translations($resolved_language);
		$submitted_strings = [];
		$string_keys = array_keys(YTCT_Strings::get_string_keys());
		
		if (isset($_POST['strings']) && is_array($_POST['strings'])) {
			foreach ($string_keys as $key) {
				if (!isset($_POST['strings'][$key])) {
					continue;
				}

				// Use strict sanitization - only allow <a> tags
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by sanitize_consent_string
				$raw_value = wp_unslash($_POST['strings'][$key]);
				if (!is_scalar($raw_value)) {
					continue;
				}

				$value = $this->sanitize_consent_string((string) $raw_value);
				if (!empty($value)) {
					$submitted_strings[$key] = $value;
				}
			}
		}

		$invalid_placeholder_fields = $this->get_invalid_placeholder_fields($submitted_strings);
		if (!empty($invalid_placeholder_fields)) {
			// translators: 1: %s placeholder, 2: %1$s placeholder, 3: field labels.
			$message = sprintf(
				__('The following fields must include %1$s or %2$s: %3$s', 'yt-consent-translations'),
				'%s',
				'%1$s',
				implode(', ', $invalid_placeholder_fields)
			);
			wp_send_json_error(['message' => $message]);
		}

		$custom_strings = $this->build_custom_string_diff($submitted_strings, $preset_translations);

		// Save options
		$options = [
			'enabled' => $enabled,
			'language' => $language,
			'custom_strings' => $custom_strings
		];

		update_option(YTCT_OPTION_NAME, $options);

		// Clear translator cache
		YTCT_Translator::get_instance()->clear_cache();

		wp_send_json_success(['message' => __('Settings saved successfully!', 'yt-consent-translations')]);
	}

	/**
	 * AJAX: Reset settings
	 */
	public function ajax_reset_settings() {
		// Check nonce
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (empty($nonce) || !wp_verify_nonce($nonce, 'ytct_admin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
		}

		// Check permissions
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
		}

		// Reset to defaults
		$defaults = [
			'enabled' => true,
			'language' => 'en',
			'custom_strings' => []
		];

		update_option(YTCT_OPTION_NAME, $defaults);

		// Clear translator cache
		YTCT_Translator::get_instance()->clear_cache();

		wp_send_json_success([
			'message' => __('Settings reset successfully!', 'yt-consent-translations'),
			'options' => $defaults
		]);
	}

	/**
	 * AJAX: Export settings (returns JSON data for download)
	 */
	public function ajax_export_settings() {
		// Check nonce (POST only for security - nonces should not be in URLs)
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (empty($nonce) || !wp_verify_nonce($nonce, 'ytct_admin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
		}

		// Check permissions
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
		}

		$options = get_option(YTCT_OPTION_NAME, []);

		// Return JSON data for client-side download
		wp_send_json_success([
			'filename' => 'yt-consent-translations-export.json',
			'data' => $options
		]);
	}

	/**
	 * AJAX: Import settings
	 */
	public function ajax_import_settings() {
		// Check nonce
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (empty($nonce) || !wp_verify_nonce($nonce, 'ytct_admin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
		}

		// Check permissions
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
		}

		// Check file exists and validate
		if (!isset($_FILES['import_file'])) {
			wp_send_json_error(['message' => __('No file uploaded.', 'yt-consent-translations')]);
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- $_FILES array is validated below
		$ytct_file = $_FILES['import_file'];

		if (!isset($ytct_file['error']) || $ytct_file['error'] !== UPLOAD_ERR_OK) {
			wp_send_json_error(['message' => __('File upload failed.', 'yt-consent-translations')]);
		}

		// Validate file size (max 100KB)
		$max_size = 100 * 1024; // 100KB
		if (!isset($ytct_file['size']) || $ytct_file['size'] > $max_size) {
			wp_send_json_error(['message' => __('File too large. Maximum size is 100KB.', 'yt-consent-translations')]);
		}

		// Validate file type
		if (!isset($ytct_file['name'])) {
			wp_send_json_error(['message' => __('Invalid file.', 'yt-consent-translations')]);
		}
		$file_info = wp_check_filetype(sanitize_file_name($ytct_file['name']));
		$allowed_extensions = ['json'];
		if (!$file_info['ext'] || !in_array(strtolower($file_info['ext']), $allowed_extensions, true)) {
			wp_send_json_error(['message' => __('Invalid file type. Only JSON files are allowed.', 'yt-consent-translations')]);
		}

		// Read file - tmp_name is a server path, not user input
		if (!isset($ytct_file['tmp_name']) || !is_uploaded_file($ytct_file['tmp_name'])) {
			wp_send_json_error(['message' => __('Invalid file upload.', 'yt-consent-translations')]);
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading uploaded temp file
		$content = file_get_contents($ytct_file['tmp_name']);
		$data = json_decode($content, true);

		if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
			wp_send_json_error(['message' => __('Invalid JSON file.', 'yt-consent-translations')]);
		}

		// Validate and sanitize data
		$options = [
			'enabled' => isset($data['enabled']) ? (bool) $data['enabled'] : true,
			'language' => 'en',
			'custom_strings' => []
		];

		// Validate language
		if (isset($data['language']) && is_scalar($data['language'])) {
			$options['language'] = $this->get_valid_language(sanitize_text_field((string) $data['language']));
		}

		$resolved_language = YTCT_Strings::resolve_language_code($options['language'], true);
		$preset_translations = YTCT_Strings::get_translations($resolved_language);
		$submitted_strings = [];

		// Validate custom strings with strict sanitization
		if (isset($data['custom_strings']) && is_array($data['custom_strings'])) {
			$string_keys = array_keys(YTCT_Strings::get_string_keys());
			foreach ($data['custom_strings'] as $key => $value) {
				if (in_array($key, $string_keys, true) && is_scalar($value)) {
					$sanitized_value = $this->sanitize_consent_string((string) $value);
					if ($sanitized_value !== '') {
						$submitted_strings[$key] = $sanitized_value;
					}
				}
			}
		}

		$invalid_placeholder_fields = $this->get_invalid_placeholder_fields($submitted_strings);
		if (!empty($invalid_placeholder_fields)) {
			// translators: 1: %s placeholder, 2: %1$s placeholder, 3: field labels.
			$message = sprintf(
				__('The following fields must include %1$s or %2$s: %3$s', 'yt-consent-translations'),
				'%s',
				'%1$s',
				implode(', ', $invalid_placeholder_fields)
			);
			wp_send_json_error(['message' => $message]);
		}

		$options['custom_strings'] = $this->build_custom_string_diff($submitted_strings, $preset_translations);

		update_option(YTCT_OPTION_NAME, $options);

		// Clear translator cache
		YTCT_Translator::get_instance()->clear_cache();

		wp_send_json_success([
			'message' => __('Settings imported successfully!', 'yt-consent-translations'),
			'options' => $options
		]);
	}

	/**
	 * AJAX: Load language preset
	 */
	public function ajax_load_language() {
		// Check nonce
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
		if (empty($nonce) || !wp_verify_nonce($nonce, 'ytct_admin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
		}

		// Check permissions
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
		}

		$language = isset($_POST['language']) ? sanitize_text_field(wp_unslash($_POST['language'])) : 'en';
		$language = $this->get_valid_language($language);
		$resolved_language = YTCT_Strings::resolve_language_code($language, true);
		$translations = YTCT_Strings::get_translations($resolved_language);

		wp_send_json_success([
			'language' => $language,
			'resolvedLanguage' => $resolved_language,
			'translations' => $translations
		]);
	}
}
