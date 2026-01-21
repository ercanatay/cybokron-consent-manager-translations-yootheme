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
     * AJAX: Save settings
     */
    public function ajax_save_settings() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ytct_admin_nonce')) {
            wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
        }

        // Get and sanitize data
        $enabled = isset($_POST['enabled']) ? (bool) $_POST['enabled'] : true;
        $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : 'en';
        
        // Validate language
        $valid_languages = array_keys(YTCT_Strings::get_languages());
        if (!in_array($language, $valid_languages)) {
            $language = 'en';
        }

        // Process custom strings
        $custom_strings = [];
        $string_keys = array_keys(YTCT_Strings::get_string_keys());
        
        foreach ($string_keys as $key) {
            if (isset($_POST['strings'][$key])) {
                $value = wp_kses_post($_POST['strings'][$key]);
                if (!empty($value)) {
                    $custom_strings[$key] = $value;
                }
            }
        }

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
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ytct_admin_nonce')) {
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
     * AJAX: Export settings
     */
    public function ajax_export_settings() {
        // Check nonce
        if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'ytct_admin_nonce')) {
            wp_die(__('Security check failed.', 'yt-consent-translations'));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Permission denied.', 'yt-consent-translations'));
        }

        $options = get_option(YTCT_OPTION_NAME, []);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="yt-consent-translations-export.json"');
        header('Pragma: no-cache');

        echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * AJAX: Import settings
     */
    public function ajax_import_settings() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ytct_admin_nonce')) {
            wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
        }

        // Check file
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => __('File upload failed.', 'yt-consent-translations')]);
        }

        // Read file
        $content = file_get_contents($_FILES['import_file']['tmp_name']);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => __('Invalid JSON file.', 'yt-consent-translations')]);
        }

        // Validate and sanitize data
        $options = [
            'enabled' => isset($data['enabled']) ? (bool) $data['enabled'] : true,
            'language' => 'en',
            'custom_strings' => []
        ];

        // Validate language
        if (isset($data['language'])) {
            $valid_languages = array_keys(YTCT_Strings::get_languages());
            if (in_array($data['language'], $valid_languages)) {
                $options['language'] = $data['language'];
            }
        }

        // Validate custom strings
        if (isset($data['custom_strings']) && is_array($data['custom_strings'])) {
            $string_keys = array_keys(YTCT_Strings::get_string_keys());
            foreach ($data['custom_strings'] as $key => $value) {
                if (in_array($key, $string_keys)) {
                    $options['custom_strings'][$key] = wp_kses_post($value);
                }
            }
        }

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
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ytct_admin_nonce')) {
            wp_send_json_error(['message' => __('Security check failed.', 'yt-consent-translations')]);
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'yt-consent-translations')]);
        }

        $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : 'en';

        // Validate language
        $valid_languages = array_keys(YTCT_Strings::get_languages());
        if (!in_array($language, $valid_languages)) {
            $language = 'en';
        }

        $translations = YTCT_Strings::get_translations($language);

        wp_send_json_success([
            'language' => $language,
            'translations' => $translations
        ]);
    }
}
