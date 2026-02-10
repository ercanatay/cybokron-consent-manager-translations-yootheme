<?php
/**
 * Options storage helper with locale scope and snapshots.
 *
 * @package YT_Consent_Translations
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class YTCT_Options
 */
class YTCT_Options {

	/**
	 * Maximum snapshot count retained per locale.
	 */
	const MAX_SNAPSHOTS = 20;

	/**
	 * Cache group used for expensive option scans.
	 */
	const CACHE_GROUP = 'ytct_options';

	/**
	 * Get normalized locale code.
	 *
	 * @param string $locale Locale value.
	 * @return string
	 */
	public static function normalize_locale($locale) {
		if (!is_string($locale) || $locale === '') {
			$locale = get_locale();
		}

		$locale = str_replace('-', '_', $locale);
		$locale = preg_replace('/[^a-zA-Z0-9_]/', '', $locale);

		if (!is_string($locale) || $locale === '') {
			return 'en_US';
		}

		return $locale;
	}

	/**
	 * Get default options payload.
	 *
	 * @return array
	 */
	public static function get_default_options() {
		return [
			'enabled' => true,
			'language' => 'en',
			'custom_strings' => [],
			'updated_at' => gmdate('c')
		];
	}

	/**
	 * Get locale-scoped option name.
	 *
	 * @param string $locale Locale value.
	 * @return string
	 */
	public static function get_option_name($locale = '') {
		$normalized = strtolower(self::normalize_locale($locale));
		$normalized = str_replace('__', '_', $normalized);
		return YTCT_OPTION_NAME . '__' . sanitize_key($normalized);
	}

	/**
	 * Get snapshot option name.
	 *
	 * @param string $locale Locale value.
	 * @return string
	 */
	public static function get_snapshot_option_name($locale = '') {
		$normalized = strtolower(self::normalize_locale($locale));
		$normalized = str_replace('__', '_', $normalized);
		return YTCT_OPTION_NAME . '_snapshots__' . sanitize_key($normalized);
	}

	/**
	 * Sanitize options payload.
	 *
	 * @param array $options Options payload.
	 * @return array
	 */
	public static function sanitize_options($options) {
		$defaults = self::get_default_options();
		if (!is_array($options)) {
			return $defaults;
		}

		$enabled = isset($options['enabled']) ? (bool) $options['enabled'] : true;
		$language = isset($options['language']) && is_scalar($options['language']) ? sanitize_text_field((string) $options['language']) : 'en';
		$language = YTCT_Strings::is_valid_language($language) ? $language : 'en';

		$custom_strings = [];
		if (isset($options['custom_strings']) && is_array($options['custom_strings'])) {
			$valid_keys = YTCT_Strings::get_string_keys();
			foreach ($options['custom_strings'] as $key => $value) {
				if (!isset($valid_keys[$key]) || !is_scalar($value)) {
					continue;
				}

				$value = (string) $value;
				if ($value !== '') {
					$custom_strings[$key] = $value;
				}
			}
		}

		return [
			'enabled' => $enabled,
			'language' => $language,
			'custom_strings' => $custom_strings,
			'updated_at' => gmdate('c')
		];
	}

	/**
	 * Get options for locale.
	 * Falls back to legacy global option for backward compatibility.
	 *
	 * @param string $locale Locale value.
	 * @return array
	 */
	public static function get_options($locale = '') {
		$option_name = self::get_option_name($locale);
		$defaults = self::get_default_options();
		$options = get_option($option_name, null);

		if (!is_array($options)) {
			$legacy = get_option(YTCT_OPTION_NAME, null);
			if (is_array($legacy)) {
				$options = $legacy;
			}
		}

		if (!is_array($options)) {
			$options = $defaults;
		}

		return self::sanitize_options($options);
	}

	/**
	 * Update options for locale and create snapshot.
	 *
	 * @param array  $options Options payload.
	 * @param string $locale Locale value.
	 * @param string $label Snapshot label.
	 * @return array
	 */
	public static function update_options($options, $locale = '', $label = 'manual_save') {
		$sanitized = self::sanitize_options($options);
		$option_name = self::get_option_name($locale);
		update_option($option_name, $sanitized);
		wp_cache_delete('all_locale_options', self::CACHE_GROUP);

		self::append_snapshot($sanitized, $locale, $label);

		return $sanitized;
	}

	/**
	 * Delete options for locale.
	 *
	 * @param string $locale Locale value.
	 * @return void
	 */
	public static function delete_options($locale = '') {
		delete_option(self::get_option_name($locale));
		wp_cache_delete('all_locale_options', self::CACHE_GROUP);
	}

	/**
	 * Append snapshot entry.
	 *
	 * @param array  $options Options payload.
	 * @param string $locale Locale value.
	 * @param string $label Snapshot label.
	 * @return void
	 */
	public static function append_snapshot($options, $locale = '', $label = 'manual_save') {
		$snapshot_name = self::get_snapshot_option_name($locale);
		$snapshots = get_option($snapshot_name, []);

		if (!is_array($snapshots)) {
			$snapshots = [];
		}

		$entry = [
			'id' => uniqid('ytct_', true),
			'created_at' => gmdate('c'),
			'label' => sanitize_key((string) $label),
			'options' => self::sanitize_options($options)
		];

		array_unshift($snapshots, $entry);
		$snapshots = array_slice($snapshots, 0, self::MAX_SNAPSHOTS);

		update_option($snapshot_name, $snapshots, false);
	}

	/**
	 * Get snapshots list for locale.
	 *
	 * @param string $locale Locale value.
	 * @return array
	 */
	public static function get_snapshots($locale = '') {
		$snapshot_name = self::get_snapshot_option_name($locale);
		$snapshots = get_option($snapshot_name, []);

		if (!is_array($snapshots)) {
			return [];
		}

		$clean = [];
		foreach ($snapshots as $snapshot) {
			if (!is_array($snapshot) || !isset($snapshot['id']) || !isset($snapshot['options'])) {
				continue;
			}

			$clean[] = [
				'id' => sanitize_text_field((string) $snapshot['id']),
				'created_at' => isset($snapshot['created_at']) ? sanitize_text_field((string) $snapshot['created_at']) : '',
				'label' => isset($snapshot['label']) ? sanitize_key((string) $snapshot['label']) : 'unknown',
				'options' => self::sanitize_options($snapshot['options'])
			];
		}

		return $clean;
	}

	/**
	 * Restore snapshot by ID.
	 *
	 * @param string $snapshot_id Snapshot ID.
	 * @param string $locale Locale value.
	 * @return array|null
	 */
	public static function restore_snapshot($snapshot_id, $locale = '') {
		$snapshot_id = sanitize_text_field((string) $snapshot_id);
		if ($snapshot_id === '') {
			return null;
		}

		$snapshots = self::get_snapshots($locale);
		foreach ($snapshots as $snapshot) {
			if (!isset($snapshot['id']) || $snapshot['id'] !== $snapshot_id) {
				continue;
			}

			$restored = self::update_options($snapshot['options'], $locale, 'rollback');
			return $restored;
		}

		return null;
	}

	/**
	 * Get summary for all locale-scoped settings.
	 *
	 * @return array
	 */
	public static function get_all_locale_options() {
		global $wpdb;

		$cached = wp_cache_get('all_locale_options', self::CACHE_GROUP);
		if (is_array($cached)) {
			return $cached;
		}

		$results = [];
		$base = YTCT_OPTION_NAME . '__';
		$escaped = $wpdb->esc_like($base) . '%';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Wildcard lookup across locale-scoped option names requires a direct query.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s",
				$escaped
			),
			ARRAY_A
		);

		if (!is_array($rows)) {
			return [];
		}

		foreach ($rows as $row) {
			if (!isset($row['option_name'])) {
				continue;
			}

			$option_name = (string) $row['option_name'];
			$locale = substr($option_name, strlen($base));
			$locale = strtoupper(str_replace('_', '-', $locale));

			$value = maybe_unserialize($row['option_value']);
			$results[$locale] = self::sanitize_options($value);
		}

		ksort($results);
		wp_cache_set('all_locale_options', $results, self::CACHE_GROUP, 300);
		return $results;
	}
}
