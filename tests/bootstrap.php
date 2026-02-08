<?php
/**
 * Lightweight WordPress stubs for local plugin tests.
 */

if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

if (!defined('YTCT_PLUGIN_DIR')) {
	define('YTCT_PLUGIN_DIR', dirname(__DIR__) . '/');
}

if (!defined('YTCT_VERSION')) {
	define('YTCT_VERSION', 'test');
}

if (!defined('YTCT_OPTION_NAME')) {
	define('YTCT_OPTION_NAME', 'yt_consent_translations');
}

$GLOBALS['ytct_option_store'] = [];

if (!function_exists('__')) {
	function __($text, $domain = null) {
		return $text;
	}
}

if (!function_exists('get_locale')) {
	function get_locale() {
		return 'en_US';
	}
}

if (!function_exists('sanitize_key')) {
	function sanitize_key($key) {
		$key = strtolower((string) $key);
		return preg_replace('/[^a-z0-9_\-]/', '', $key);
	}
}

if (!function_exists('sanitize_text_field')) {
	function sanitize_text_field($text) {
		$text = (string) $text;
		$text = strip_tags($text);
		return trim($text);
	}
}

if (!function_exists('wp_kses')) {
	function wp_kses($text, $allowed_html = []) {
		return (string) $text;
	}
}

if (!function_exists('wp_parse_args')) {
	function wp_parse_args($args, $defaults = []) {
		return array_merge($defaults, (array) $args);
	}
}

if (!function_exists('add_action')) {
	function add_action($hook, $callable) {
		return true;
	}
}

if (!function_exists('get_option')) {
	function get_option($option, $default = false) {
		if (array_key_exists($option, $GLOBALS['ytct_option_store'])) {
			return $GLOBALS['ytct_option_store'][$option];
		}

		return $default;
	}
}

if (!function_exists('update_option')) {
	function update_option($option, $value) {
		$GLOBALS['ytct_option_store'][$option] = $value;
		return true;
	}
}

if (!function_exists('delete_option')) {
	function delete_option($option) {
		unset($GLOBALS['ytct_option_store'][$option]);
		return true;
	}
}

if (!function_exists('maybe_unserialize')) {
	function maybe_unserialize($value) {
		return $value;
	}
}

if (!function_exists('esc_sql')) {
	function esc_sql($text) {
		return $text;
	}
}

if (!function_exists('apply_filters')) {
	function apply_filters($tag, $value) {
		return $value;
	}
}

if (!function_exists('wp_get_theme')) {
	function wp_get_theme() {
		return new class {
			public function get($key) {
				if ($key === 'Name') {
					return 'YOOtheme Pro';
				}
				return '';
			}

			public function get_template() {
				return 'yootheme';
			}
		};
	}
}
