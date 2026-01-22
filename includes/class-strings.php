<?php
/**
 * String definitions for all supported languages
 *
 * @package YT_Consent_Translations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class YTCT_Strings
 * Contains all translation strings for supported languages
 * Translations are loaded from external JSON files for better memory management
 */
class YTCT_Strings {

	/**
	 * Available languages
	 */
	private static $languages = [
		'auto' => 'Auto (WordPress Default)',
		'en' => 'English',
		'zh' => '中文',
		'es' => 'Español',
		'fr' => 'Français',
		'pt' => 'Português',
		'ru' => 'Русский',
		'ja' => '日本語',
		'id' => 'Bahasa Indonesia',
		'it' => 'Italiano',
		'nl' => 'Nederlands',
		'pl' => 'Polski',
		'vi' => 'Tiếng Việt',
		'th' => 'ไทย',
		'uk' => 'Українська',
		'cs' => 'Čeština',
		'el' => 'Ελληνικά',
		'ro' => 'Română',
		'hu' => 'Magyar',
		'sv' => 'Svenska',
		'da' => 'Dansk',
		'fi' => 'Suomi',
		'nb' => 'Norsk',
		'he' => 'עברית',
		'ms' => 'Bahasa Melayu',
		'bn' => 'বাংলা',
		'fa' => 'فارسی',
		'ta' => 'தமிழ்',
		'te' => 'తెలుగు',
		'mr' => 'मराठी',
		'sw' => 'Kiswahili',
		'tl' => 'Filipino',
		'tr' => 'Türkçe',
		'hi' => 'हिन्दी',
		'ko' => '한국어',
		'ar' => 'العربية',
		'de' => 'Deutsch'
	];

	/**
	 * WordPress locale to plugin language code mapping
	 */
	private static $locale_map = [
		'en_US' => 'en', 'en_GB' => 'en', 'en_AU' => 'en', 'en_CA' => 'en',
		'zh_CN' => 'zh', 'zh_TW' => 'zh', 'zh_HK' => 'zh',
		'es_ES' => 'es', 'es_MX' => 'es', 'es_AR' => 'es', 'es_CO' => 'es',
		'fr_FR' => 'fr', 'fr_CA' => 'fr', 'fr_BE' => 'fr',
		'pt_BR' => 'pt', 'pt_PT' => 'pt',
		'ru_RU' => 'ru',
		'ja' => 'ja', 'ja_JP' => 'ja',
		'id_ID' => 'id',
		'it_IT' => 'it',
		'nl_NL' => 'nl', 'nl_BE' => 'nl',
		'pl_PL' => 'pl',
		'vi' => 'vi', 'vi_VN' => 'vi',
		'th' => 'th', 'th_TH' => 'th',
		'uk' => 'uk', 'uk_UA' => 'uk',
		'cs_CZ' => 'cs',
		'el' => 'el', 'el_GR' => 'el',
		'ro_RO' => 'ro',
		'hu_HU' => 'hu',
		'sv_SE' => 'sv',
		'da_DK' => 'da',
		'fi' => 'fi', 'fi_FI' => 'fi',
		'nb_NO' => 'nb', 'nn_NO' => 'nb',
		'he_IL' => 'he',
		'ms_MY' => 'ms',
		'bn_BD' => 'bn',
		'fa_IR' => 'fa',
		'ta_IN' => 'ta', 'ta_LK' => 'ta',
		'te_IN' => 'te',
		'mr_IN' => 'mr',
		'sw' => 'sw', 'sw_KE' => 'sw',
		'tl' => 'tl', 'fil' => 'tl',
		'tr_TR' => 'tr',
		'hi_IN' => 'hi',
		'ko_KR' => 'ko',
		'ar' => 'ar', 'ar_SA' => 'ar', 'ar_AE' => 'ar', 'ar_EG' => 'ar',
		'de_DE' => 'de', 'de_AT' => 'de', 'de_CH' => 'de', 'de_DE_formal' => 'de'
	];

	/**
	 * String keys with their original English text
	 */
	private static $string_keys = [
		'banner_text' => 'We use cookies and similar technologies to improve your experience on our website.',
		'banner_link' => 'Read our <a href="%s">Privacy Policy</a>.',
		'button_accept' => 'Accept',
		'button_reject' => 'Reject',
		'button_settings' => 'Manage Settings',
		'modal_title' => 'Privacy Settings',
		'modal_content' => 'This website uses cookies and similar technologies. They are grouped into categories, which you can review and manage below. If you have accepted any non-essential cookies, you can change your preferences at any time in the settings.',
		'modal_content_link' => 'Learn more in our <a href="%s">Privacy Policy</a>.',
		'functional_title' => 'Functional',
		'preferences_title' => 'Preferences',
		'statistics_title' => 'Statistics',
		'marketing_title' => 'Marketing',
		'functional_content' => 'These technologies are required to activate the core functionality of our website.',
		'preferences_content' => 'These technologies allow our website to remember your preferences and provide you with a more personalized experience.',
		'statistics_content' => 'These technologies enable us to analyse the use of our website in order to measure and improve performance.',
		'marketing_content' => 'These technologies are used by our marketing partners to show you personalized advertisements relevant to your interests.',
		'show_services' => 'Show Services',
		'hide_services' => 'Hide Services',
		'modal_accept' => 'Accept all',
		'modal_reject' => 'Reject all',
		'modal_save' => 'Save'
	];

	/**
	 * Get available languages
	 *
	 * @return array
	 */
	public static function get_languages() {
		return self::$languages;
	}

	/**
	 * Base language code mapping (2-letter codes to plugin language codes)
	 * Used as fallback when exact locale is not found
	 */
	private static $base_lang_map = [
		'en' => 'en', 'zh' => 'zh', 'es' => 'es', 'fr' => 'fr',
		'pt' => 'pt', 'ru' => 'ru', 'ja' => 'ja', 'id' => 'id',
		'it' => 'it', 'nl' => 'nl', 'pl' => 'pl', 'vi' => 'vi',
		'th' => 'th', 'uk' => 'uk', 'cs' => 'cs', 'el' => 'el',
		'ro' => 'ro', 'hu' => 'hu', 'sv' => 'sv', 'da' => 'da',
		'fi' => 'fi', 'nb' => 'nb', 'nn' => 'nb', 'no' => 'nb',
		'he' => 'he', 'ms' => 'ms', 'bn' => 'bn', 'fa' => 'fa',
		'ta' => 'ta', 'te' => 'te', 'mr' => 'mr', 'sw' => 'sw',
		'tl' => 'tl', 'tr' => 'tr', 'hi' => 'hi', 'ko' => 'ko',
		'ar' => 'ar', 'de' => 'de'
	];

	/**
	 * Cache for loaded translations (per-language)
	 */
	private static $translations_cache = [];

	/**
	 * Detect language from WordPress locale
	 *
	 * @return string Language code
	 */
	public static function detect_wp_language() {
		$locale = get_locale();
		
		// 1. Direct match in locale_map (most specific)
		if (isset(self::$locale_map[$locale])) {
			return self::$locale_map[$locale];
		}
		
		// 2. Try base language from explicit mapping (deterministic)
		$base_lang = substr($locale, 0, 2);
		if (isset(self::$base_lang_map[$base_lang])) {
			return self::$base_lang_map[$base_lang];
		}
		
		// 3. Default to English
		return 'en';
	}

	/**
	 * Get locale map
	 *
	 * @return array
	 */
	public static function get_locale_map() {
		return self::$locale_map;
	}

	/**
	 * Get string keys with original text
	 *
	 * @return array
	 */
	public static function get_string_keys() {
		return self::$string_keys;
	}

	/**
	 * Get original English text by key
	 *
	 * @param string $key String key
	 * @return string|null
	 */
	public static function get_original($key) {
		return isset(self::$string_keys[$key]) ? self::$string_keys[$key] : null;
	}

	/**
	 * Get path to language JSON file
	 *
	 * @param string $lang Language code
	 * @return string
	 */
	private static function get_language_file_path($lang) {
		return YTCT_PLUGIN_DIR . 'languages/' . $lang . '.json';
	}

	/**
	 * Load translations from JSON file
	 *
	 * @param string $lang Language code
	 * @return array|null
	 */
	private static function load_language_file($lang) {
		$file_path = self::get_language_file_path($lang);
		
		if (!file_exists($file_path)) {
			return null;
		}
		
		$content = file_get_contents($file_path);
		if ($content === false) {
			return null;
		}
		
		$translations = json_decode($content, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return null;
		}
		
		return $translations;
	}

	/**
	 * Get translations for a specific language (lazy loading)
	 *
	 * @param string $lang Language code
	 * @return array
	 */
	public static function get_translations($lang = 'en') {
		// Validate language code
		if (!isset(self::$languages[$lang]) || $lang === 'auto') {
			$lang = 'en';
		}
		
		// Return from cache if available
		if (isset(self::$translations_cache[$lang])) {
			return self::$translations_cache[$lang];
		}
		
		// Load from JSON file
		$translations = self::load_language_file($lang);
		
		// Fallback to English if loading failed
		if ($translations === null) {
			if ($lang !== 'en') {
				$translations = self::load_language_file('en');
			}
			
			// Ultimate fallback to string_keys (original English)
			if ($translations === null) {
				$translations = self::$string_keys;
			}
		}
		
		// Cache the loaded translations
		self::$translations_cache[$lang] = $translations;
		
		return $translations;
	}

	/**
	 * Get all translations for all languages
	 * Note: This loads all JSON files - use sparingly (mainly for admin export)
	 *
	 * @return array
	 */
	public static function get_all_translations() {
		$all_translations = [];
		
		foreach (self::$languages as $code => $name) {
			if ($code === 'auto') {
				continue;
			}
			
			$all_translations[$code] = self::get_translations($code);
		}
		
		return $all_translations;
	}

	/**
	 * Clear translations cache
	 *
	 * @return void
	 */
	public static function clear_cache() {
		self::$translations_cache = [];
	}

	/**
	 * Get string groups for admin UI organization
	 *
	 * @return array
	 */
	public static function get_string_groups() {
		return [
			'banner' => [
				'label' => __('Banner', 'yt-consent-translations'),
				'keys' => ['banner_text', 'banner_link', 'button_accept', 'button_reject', 'button_settings']
			],
			'modal' => [
				'label' => __('Modal', 'yt-consent-translations'),
				'keys' => ['modal_title', 'modal_content', 'modal_content_link']
			],
			'categories' => [
				'label' => __('Categories', 'yt-consent-translations'),
				'keys' => [
					'functional_title', 'functional_content',
					'preferences_title', 'preferences_content',
					'statistics_title', 'statistics_content',
					'marketing_title', 'marketing_content'
				]
			],
			'buttons' => [
				'label' => __('Buttons', 'yt-consent-translations'),
				'keys' => ['show_services', 'hide_services', 'modal_accept', 'modal_reject', 'modal_save']
			]
		];
	}

	/**
	 * Get human-readable label for a string key
	 *
	 * @param string $key String key
	 * @return string
	 */
	public static function get_key_label($key) {
		$labels = [
			'banner_text' => __('Banner Text', 'yt-consent-translations'),
			'banner_link' => __('Privacy Policy Link', 'yt-consent-translations'),
			'button_accept' => __('Accept Button', 'yt-consent-translations'),
			'button_reject' => __('Reject Button', 'yt-consent-translations'),
			'button_settings' => __('Settings Button', 'yt-consent-translations'),
			'modal_title' => __('Modal Title', 'yt-consent-translations'),
			'modal_content' => __('Modal Content', 'yt-consent-translations'),
			'modal_content_link' => __('Modal Privacy Link', 'yt-consent-translations'),
			'functional_title' => __('Functional Title', 'yt-consent-translations'),
			'preferences_title' => __('Preferences Title', 'yt-consent-translations'),
			'statistics_title' => __('Statistics Title', 'yt-consent-translations'),
			'marketing_title' => __('Marketing Title', 'yt-consent-translations'),
			'functional_content' => __('Functional Description', 'yt-consent-translations'),
			'preferences_content' => __('Preferences Description', 'yt-consent-translations'),
			'statistics_content' => __('Statistics Description', 'yt-consent-translations'),
			'marketing_content' => __('Marketing Description', 'yt-consent-translations'),
			'show_services' => __('Show Services', 'yt-consent-translations'),
			'hide_services' => __('Hide Services', 'yt-consent-translations'),
			'modal_accept' => __('Accept All Button', 'yt-consent-translations'),
			'modal_reject' => __('Reject All Button', 'yt-consent-translations'),
			'modal_save' => __('Save Button', 'yt-consent-translations')
		];

		return isset($labels[$key]) ? $labels[$key] : $key;
	}

	/**
	 * Check if a string key contains HTML placeholder
	 *
	 * @param string $key String key
	 * @return bool
	 */
	public static function has_placeholder($key) {
		static $placeholders = [
			'banner_link' => true,
			'modal_content_link' => true
		];
		return isset($placeholders[$key]);
	}
}
