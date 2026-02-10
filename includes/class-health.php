<?php
/**
 * Compatibility and quality health reporting.
 *
 * @package YT_Consent_Translations
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class YTCT_Health
 */
class YTCT_Health {

	/**
	 * Health report option key.
	 */
	const OPTION_NAME = 'ytct_health_report';

	/**
	 * In-request mutable report.
	 *
	 * @var array|null
	 */
	private static $report = null;

	/**
	 * Dirty flag for persistence.
	 *
	 * @var bool
	 */
	private static $dirty = false;

	/**
	 * Register lifecycle hooks.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action('shutdown', [__CLASS__, 'persist']);
	}

	/**
	 * Get default report payload.
	 *
	 * @return array
	 */
	private static function get_default_report() {
		return [
			'last_checked_at' => '',
			'last_match_at' => '',
			'matched_count' => 0,
			'unmatched_count' => 0,
			'unmatched_strings' => [],
			'plugin_version' => YTCT_VERSION
		];
	}

	/**
	 * Load report from option.
	 *
	 * @return array
	 */
	public static function get_report() {
		if (self::$report !== null) {
			return self::$report;
		}

		$report = get_option(self::OPTION_NAME, []);
		if (!is_array($report)) {
			$report = [];
		}

		$report = wp_parse_args($report, self::get_default_report());

		if (!is_array($report['unmatched_strings'])) {
			$report['unmatched_strings'] = [];
		}

		self::$report = $report;
		return self::$report;
	}

	/**
	 * Record a successful string match.
	 *
	 * @return void
	 */
	public static function record_match() {
		$report = self::get_report();
		$report['matched_count'] = isset($report['matched_count']) ? (int) $report['matched_count'] + 1 : 1;
		$report['last_match_at'] = gmdate('c');
		$report['last_checked_at'] = gmdate('c');
		$report['plugin_version'] = YTCT_VERSION;
		self::$report = $report;
		self::$dirty = true;
	}

	/**
	 * Record an unmatched but consent-related string.
	 *
	 * @param string $original Original gettext source.
	 * @return void
	 */
	public static function record_unmatched($original) {
		if (!self::looks_like_consent_string($original)) {
			return;
		}

		$original = trim((string) $original);
		if ($original === '') {
			return;
		}

		$report = self::get_report();
		$report['unmatched_count'] = isset($report['unmatched_count']) ? (int) $report['unmatched_count'] + 1 : 1;
		$report['last_checked_at'] = gmdate('c');
		$report['plugin_version'] = YTCT_VERSION;

		if (!isset($report['unmatched_strings'][$original])) {
			if (count($report['unmatched_strings']) < 20) {
				$report['unmatched_strings'][$original] = 1;
			}
		} else {
			$report['unmatched_strings'][$original] = (int) $report['unmatched_strings'][$original] + 1;
		}

		self::$report = $report;
		self::$dirty = true;
	}

	/**
	 * Determine whether a string is likely consent-related.
	 *
	 * @param string $original Original source.
	 * @return bool
	 */
	private static function looks_like_consent_string($original) {
		$original = strtolower((string) $original);
		if ($original === '') {
			return false;
		}

		$keywords = [
			'cookie',
			'privacy',
			'consent',
			'functional',
			'preferences',
			'statistics',
			'marketing',
			'accept',
			'reject',
			'save',
			'service',
			'settings'
		];

		foreach ($keywords as $keyword) {
			if (strpos($original, $keyword) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Build compatibility health summary.
	 *
	 * @param bool $enabled Whether plugin translations are enabled.
	 * @return array
	 */
	public static function build_summary($enabled = true) {
		$report = self::get_report();
		$issues = [];
		$warnings = [];

		$theme = wp_get_theme();
		$theme_name = is_object($theme) ? (string) $theme->get('Name') : '';
		$template = is_object($theme) ? (string) $theme->get_template() : '';
		$is_yootheme = stripos($theme_name, 'yootheme') !== false || stripos($template, 'yootheme') !== false;

		if (!$is_yootheme) {
			$warnings[] = __('Active theme does not appear to be YOOtheme. Translation interception may stay inactive.', 'yt-consent-translations-main');
		}

		if ($enabled && (int) $report['matched_count'] === 0) {
			$warnings[] = __('No matching consent strings have been intercepted yet. Open a frontend page with the consent banner to verify compatibility.', 'yt-consent-translations-main');
		}

		if (!empty($report['unmatched_strings'])) {
			$issues[] = __('Potential consent-related YOOtheme source strings were detected but not matched by this plugin. YOOtheme may have changed wording.', 'yt-consent-translations-main');
		}

		$status = 'healthy';
		if (!empty($issues)) {
			$status = 'warning';
		} elseif (!empty($warnings)) {
			$status = 'notice';
		}

		return [
			'status' => $status,
			'issues' => $issues,
			'warnings' => $warnings,
			'report' => $report,
			'is_yootheme' => $is_yootheme,
			'checked_at' => gmdate('c')
		];
	}

	/**
	 * Persist report if modified.
	 *
	 * @return void
	 */
	public static function persist() {
		if (!self::$dirty || self::$report === null) {
			return;
		}

		update_option(self::OPTION_NAME, self::$report, false);
		self::$dirty = false;
	}

	/**
	 * Reset collected health stats.
	 *
	 * @return void
	 */
	public static function reset_report() {
		self::$report = self::get_default_report();
		self::$dirty = true;
	}
}
