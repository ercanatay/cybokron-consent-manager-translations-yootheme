<?php
/**
 * Plugin Name: YT Consent Translations
 * Plugin URI: https://github.com/ercanatay/yt-consent-translations
 * Description: Easily translate YOOtheme Pro 5 Consent Manager texts from the WordPress admin panel. Supports multiple languages including English, Turkish, Hindi, Korean, Arabic, and German.
 * Version: 1.3.5
 * Author: Ercan ATAY
 * Author URI: https://www.ercanatay.com/en/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: yt-consent-translations-1.3.5
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

// Plugin constants
define('YTCT_VERSION', '1.3.5');
define('YTCT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('YTCT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('YTCT_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('YTCT_OPTION_NAME', 'yt_consent_translations');

/**
 * Main plugin class
 */
final class YT_Consent_Translations {

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
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required files
	 */
	private function load_dependencies() {
		require_once YTCT_PLUGIN_DIR . 'includes/class-strings.php';
		require_once YTCT_PLUGIN_DIR . 'includes/class-options.php';
		require_once YTCT_PLUGIN_DIR . 'includes/class-health.php';
		require_once YTCT_PLUGIN_DIR . 'includes/class-translator.php';
		require_once YTCT_PLUGIN_DIR . 'includes/class-updater.php';
		require_once YTCT_PLUGIN_DIR . 'includes/class-admin.php';
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Load text domain
		add_action('plugins_loaded', [$this, 'load_textdomain']);

		// Initialize translator on frontend and admin
		add_action('init', [$this, 'init_updater']);
		add_action('init', [$this, 'init_translator']);
		add_action('init', [$this, 'init_health_monitor']);

		// Initialize admin - must run before admin_menu hook
		if (is_admin()) {
			$this->init_admin();
		}

		// Activation hook
		register_activation_hook(__FILE__, [$this, 'activate']);

		// Deactivation hook
		register_deactivation_hook(__FILE__, [$this, 'deactivate']);

		// Add settings link on plugins page
		add_filter('plugin_action_links_' . YTCT_PLUGIN_BASENAME, [$this, 'add_settings_link']);
	}

	/**
	 * Initialize updater integration.
	 *
	 * @return void
	 */
	public function init_updater() {
		YTCT_Updater::boot();
	}

	/**
	 * Load plugin text domain
	 * Note: Since WordPress 4.6, translations are automatically loaded from WordPress.org
	 * This method is kept for backward compatibility with self-hosted translations
	 */
	public function load_textdomain() {
		// Translations are automatically loaded by WordPress 4.6+ for plugins hosted on WordPress.org
		// Manual loading only needed for custom translation files not on WordPress.org
	}

	/**
	 * Initialize translator (only if enabled)
	 */
	public function init_translator() {
		if (defined('YTCT_DISABLED') && YTCT_DISABLED) {
			return;
		}

		// Skip translator initialization if disabled (performance optimization)
		$options = YTCT_Options::get_options();
		if (empty($options['enabled'])) {
			return;
		}
		
		YTCT_Translator::get_instance();
	}

	/**
	 * Initialize health monitor.
	 *
	 * @return void
	 */
	public function init_health_monitor() {
		YTCT_Health::boot();
	}

	/**
	 * Initialize admin
	 */
	public function init_admin() {
		YTCT_Admin::get_instance();
	}

	/**
	 * Plugin activation
	 */
	public function activate() {
		$locale_option_name = YTCT_Options::get_option_name();
		if (false === get_option($locale_option_name)) {
			YTCT_Options::update_options(YTCT_Options::get_default_options(), '', 'activation');
		}

		YTCT_Updater::on_activation();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		YTCT_Updater::on_deactivation();
		flush_rewrite_rules();
	}

	/**
	 * Add settings link to plugins page
	 */
	public function add_settings_link($links) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url('options-general.php?page=yt-consent-translations'),
			__('Settings', 'yt-consent-translations-1.3.5')
		);
		array_unshift($links, $settings_link);
		return $links;
	}
}

/**
 * Initialize plugin
 */
function ytct_init() {
	return YT_Consent_Translations::get_instance();
}

// Start the plugin
ytct_init();
