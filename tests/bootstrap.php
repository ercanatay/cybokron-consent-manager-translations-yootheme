<?php
// phpcs:ignoreFile -- Development test bootstrap with intentional WordPress core function shims.
/**
 * Lightweight WordPress stubs for local plugin tests.
 */

if (!defined('ABSPATH') && PHP_SAPI !== 'cli') {
	exit;
}

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

if (!defined('YTCT_PLUGIN_BASENAME')) {
	define('YTCT_PLUGIN_BASENAME', 'yt-consent-translations/yt-consent-translations.php');
}

$GLOBALS['ytct_option_store'] = [];
$GLOBALS['ytct_transient_store'] = [];
$GLOBALS['ytct_site_transient_store'] = [];
$GLOBALS['ytct_scheduled_events'] = [];
$GLOBALS['ytct_send_nosniff_calls'] = 0;
$GLOBALS['ytct_send_frame_options_calls'] = 0;

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

if (!function_exists('wp_strip_all_tags')) {
	function wp_strip_all_tags($text) {
		return preg_replace('/<[^>]*>/', '', (string) $text);
	}
}

if (!function_exists('sanitize_text_field')) {
	function sanitize_text_field($text) {
		$text = (string) $text;
		$text = wp_strip_all_tags($text);
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

if (!function_exists('add_filter')) {
	function add_filter($hook, $callable, $priority = 10, $accepted_args = 1) {
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
	function update_option($option, $value, $autoload = null) {
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

if (!function_exists('wp_cache_get')) {
	function wp_cache_get($key, $group = '') {
		$cache_key = $group . '::' . $key;
		return array_key_exists($cache_key, $GLOBALS['ytct_option_store']) ? $GLOBALS['ytct_option_store'][$cache_key] : false;
	}
}

if (!function_exists('wp_cache_set')) {
	function wp_cache_set($key, $value, $group = '', $expire = 0) {
		$cache_key = $group . '::' . $key;
		$GLOBALS['ytct_option_store'][$cache_key] = $value;
		return true;
	}
}

if (!function_exists('wp_cache_delete')) {
	function wp_cache_delete($key, $group = '') {
		$cache_key = $group . '::' . $key;
		unset($GLOBALS['ytct_option_store'][$cache_key]);
		return true;
	}
}

if (!function_exists('get_transient')) {
	function get_transient($transient) {
		if (array_key_exists($transient, $GLOBALS['ytct_transient_store'])) {
			return $GLOBALS['ytct_transient_store'][$transient];
		}
		return false;
	}
}

if (!function_exists('set_transient')) {
	function set_transient($transient, $value, $expiration = 0) {
		$GLOBALS['ytct_transient_store'][$transient] = $value;
		return true;
	}
}

if (!function_exists('delete_transient')) {
	function delete_transient($transient) {
		unset($GLOBALS['ytct_transient_store'][$transient]);
		return true;
	}
}

if (!function_exists('get_site_transient')) {
	function get_site_transient($transient) {
		if (array_key_exists($transient, $GLOBALS['ytct_site_transient_store'])) {
			return $GLOBALS['ytct_site_transient_store'][$transient];
		}
		return false;
	}
}

if (!function_exists('set_site_transient')) {
	function set_site_transient($transient, $value, $expiration = 0) {
		$GLOBALS['ytct_site_transient_store'][$transient] = $value;
		return true;
	}
}

if (!class_exists('WP_Error')) {
	class WP_Error {
		private $code;
		private $message;

		public function __construct($code = '', $message = '') {
			$this->code = $code;
			$this->message = (string) $message;
		}

		public function get_error_message() {
			return $this->message;
		}
	}
}

if (!function_exists('is_wp_error')) {
	function is_wp_error($thing) {
		return $thing instanceof WP_Error;
	}
}

if (!function_exists('wp_remote_get')) {
	function wp_remote_get($url, $args = []) {
		if (isset($GLOBALS['ytct_remote_get_mock_callback']) && is_callable($GLOBALS['ytct_remote_get_mock_callback'])) {
			return call_user_func($GLOBALS['ytct_remote_get_mock_callback'], $url, $args);
		}

		if (isset($GLOBALS['ytct_remote_get_mock_queue']) && is_array($GLOBALS['ytct_remote_get_mock_queue']) && !empty($GLOBALS['ytct_remote_get_mock_queue'])) {
			return array_shift($GLOBALS['ytct_remote_get_mock_queue']);
		}

		if (array_key_exists('ytct_remote_get_mock', $GLOBALS)) {
			return $GLOBALS['ytct_remote_get_mock'];
		}

		return [
			'response' => ['code' => 200],
			'body' => '{}'
		];
	}
}

if (!function_exists('wp_remote_retrieve_response_code')) {
	function wp_remote_retrieve_response_code($response) {
		if (is_array($response) && isset($response['response']['code'])) {
			return (int) $response['response']['code'];
		}
		return 0;
	}
}

if (!function_exists('wp_remote_retrieve_body')) {
	function wp_remote_retrieve_body($response) {
		if (is_array($response) && isset($response['body']) && is_string($response['body'])) {
			return $response['body'];
		}
		return '';
	}
}

if (!function_exists('wp_next_scheduled')) {
	function wp_next_scheduled($hook) {
		$events = isset($GLOBALS['ytct_scheduled_events']) && is_array($GLOBALS['ytct_scheduled_events']) ? $GLOBALS['ytct_scheduled_events'] : [];
		$timestamps = [];
		foreach ($events as $event) {
			if (isset($event['hook']) && $event['hook'] === $hook && isset($event['timestamp'])) {
				$timestamps[] = (int) $event['timestamp'];
			}
		}
		if (empty($timestamps)) {
			return false;
		}
		sort($timestamps);
		return $timestamps[0];
	}
}

if (!function_exists('wp_schedule_event')) {
	function wp_schedule_event($timestamp, $recurrence, $hook, $args = []) {
		$key = $hook . ':' . (int) $timestamp;
		$GLOBALS['ytct_scheduled_events'][$key] = [
			'timestamp' => (int) $timestamp,
			'recurrence' => (string) $recurrence,
			'hook' => (string) $hook,
			'args' => is_array($args) ? $args : []
		];
		return true;
	}
}

if (!function_exists('wp_unschedule_event')) {
	function wp_unschedule_event($timestamp, $hook, $args = []) {
		$key = $hook . ':' . (int) $timestamp;
		unset($GLOBALS['ytct_scheduled_events'][$key]);
		return true;
	}
}

if (!function_exists('home_url')) {
	function home_url($path = '') {
		$path = (string) $path;
		return 'https://example.test' . $path;
	}
}

if (!function_exists('esc_url_raw')) {
	function esc_url_raw($url) {
		return (string) $url;
	}
}

if (!function_exists('untrailingslashit')) {
	function untrailingslashit($string) {
		return rtrim((string) $string, '/\\');
	}
}

if (!function_exists('wp_clean_plugins_cache')) {
	function wp_clean_plugins_cache($clear_update_cache = true) {
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

if (!function_exists('send_nosniff_header')) {
	function send_nosniff_header() {
		$GLOBALS['ytct_send_nosniff_calls'] = isset($GLOBALS['ytct_send_nosniff_calls']) ? (int) $GLOBALS['ytct_send_nosniff_calls'] + 1 : 1;
	}
}

if (!function_exists('send_frame_options_header')) {
	function send_frame_options_header() {
		$GLOBALS['ytct_send_frame_options_calls'] = isset($GLOBALS['ytct_send_frame_options_calls']) ? (int) $GLOBALS['ytct_send_frame_options_calls'] + 1 : 1;
	}
}
