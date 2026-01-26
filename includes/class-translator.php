<?php
/**
 * Translator class - handles gettext filtering
 *
 * @package YT_Consent_Translations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class YTCT_Translator
 * Filters YOOtheme consent manager strings
 */
class YTCT_Translator {

	/**
	 * Single instance
	 */
	private static $instance = null;

	/**
	 * Plugin options
	 */
	private $options = null;

	/**
	 * Active translations (cached)
	 */
	private $translations = null;

	/**
	 * Original string to key mapping (cached)
	 */
	private $original_to_key = null;

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
		// Only apply filters if enabled
		if ($this->is_enabled()) {
			// Main gettext filter - priority 20 to run after other plugins
			add_filter('gettext', [$this, 'filter_gettext'], 20, 3);
			
			// gettext with context filter
			add_filter('gettext_with_context', [$this, 'filter_gettext_with_context'], 20, 4);
		}
	}

	/**
	 * Check if translation is enabled
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$options = $this->get_options();
		return isset($options['enabled']) ? (bool) $options['enabled'] : true;
	}

	/**
	 * Get plugin options
	 *
	 * @return array
	 */
	private function get_options() {
		if (null === $this->options) {
			$this->options = get_option(YTCT_OPTION_NAME, [
				'enabled' => true,
				'language' => 'en',
				'custom_strings' => []
			]);
		}
		return $this->options;
	}

	/**
	 * Get active translations
	 *
	 * @return array
	 */
	private function get_translations() {
		if (null === $this->translations) {
			$options = $this->get_options();
			$language = isset($options['language']) ? $options['language'] : 'en';
			$custom_strings = isset($options['custom_strings']) ? $options['custom_strings'] : [];

			// Auto-detect WordPress language if set to 'auto'
			if ($language === 'auto') {
				$language = YTCT_Strings::detect_wp_language();
			}

			// Start with preset translations for selected language
			$this->translations = YTCT_Strings::get_translations($language);

			// Override with any custom strings
			if (!empty($custom_strings) && is_array($custom_strings)) {
				foreach ($custom_strings as $key => $value) {
					if (!empty($value)) {
						$this->translations[$key] = $value;
					}
				}
			}
		}
		return $this->translations;
	}

	/**
	 * Get original string to key mapping
	 *
	 * @return array
	 */
	private function get_original_to_key_map() {
		if (null === $this->original_to_key) {
			$this->original_to_key = [];
			$string_keys = YTCT_Strings::get_string_keys();
			
			foreach ($string_keys as $key => $original) {
				$this->original_to_key[$original] = $key;
			}
		}
		return $this->original_to_key;
	}

	/**
	 * Filter gettext - main translation hook
	 *
	 * @param string $translated Translated text
	 * @param string $original Original text
	 * @param string $domain Text domain
	 * @return string
	 */
	public function filter_gettext($translated, $original, $domain) {
		// Early return if not yootheme domain
		if ($domain !== 'yootheme') {
			return $translated;
		}

		// Get key for this original string
		$map = $this->get_original_to_key_map();
		
		if (!isset($map[$original])) {
			return $translated;
		}

		$key = $map[$original];
		$translations = $this->get_translations();

		// Return custom translation if exists
		if (isset($translations[$key]) && !empty($translations[$key])) {
			return $translations[$key];
		}

		return $translated;
	}

	/**
	 * Filter gettext with context
	 *
	 * @param string $translated Translated text
	 * @param string $original Original text
	 * @param string $context Context
	 * @param string $domain Text domain
	 * @return string
	 */
	public function filter_gettext_with_context($translated, $original, $context, $domain) {
		// Early return if not yootheme domain
		if ($domain !== 'yootheme') {
			return $translated;
		}

		// Use same logic as regular gettext
		return $this->filter_gettext($translated, $original, $domain);
	}

	/**
	 * Clear cached data (call after saving options)
	 */
	public function clear_cache() {
		$this->options = null;
		$this->translations = null;
	}

	/**
	 * Get current active language
	 *
	 * @return string
	 */
	public function get_active_language() {
		$options = $this->get_options();
		return isset($options['language']) ? $options['language'] : 'en';
	}
}
