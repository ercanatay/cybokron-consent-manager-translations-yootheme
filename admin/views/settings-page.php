<?php
/**
 * Admin Settings Page Template
 *
 * @package CYBOCOMA_Consent_Translations
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!current_user_can('manage_options')) {
	return;
}

$cybocoma_scope_locale = CYBOCOMA_Options::normalize_locale(get_locale());
$cybocoma_scope_locale_input = filter_input(INPUT_GET, 'scope_locale', FILTER_DEFAULT);
if (is_scalar($cybocoma_scope_locale_input) && $cybocoma_scope_locale_input !== '') {
	$cybocoma_scope_locale = CYBOCOMA_Options::normalize_locale(sanitize_text_field(wp_unslash((string) $cybocoma_scope_locale_input)));
}

$cybocoma_options = CYBOCOMA_Options::get_options($cybocoma_scope_locale);
$cybocoma_enabled = isset($cybocoma_options['enabled']) ? (bool) $cybocoma_options['enabled'] : true;
$cybocoma_current_language = isset($cybocoma_options['language']) ? $cybocoma_options['language'] : 'en';
$cybocoma_custom_strings = isset($cybocoma_options['custom_strings']) && is_array($cybocoma_options['custom_strings']) ? $cybocoma_options['custom_strings'] : [];

$cybocoma_languages = CYBOCOMA_Strings::get_languages();
if (!isset($cybocoma_languages[$cybocoma_current_language])) {
	$cybocoma_current_language = 'en';
}

$cybocoma_scope_locales = CYBOCOMA_Strings::get_supported_wp_locales();
$cybocoma_scope_lookup = array_fill_keys($cybocoma_scope_locales, true);
if (!isset($cybocoma_scope_lookup[$cybocoma_scope_locale])) {
	$cybocoma_scope_locales[] = $cybocoma_scope_locale;
	sort($cybocoma_scope_locales);
}

$cybocoma_detected_language = CYBOCOMA_Strings::detect_language_from_locale($cybocoma_scope_locale);
$cybocoma_effective_language = $cybocoma_current_language === 'auto'
	? $cybocoma_detected_language
	: CYBOCOMA_Strings::resolve_language_code($cybocoma_current_language, false);
$cybocoma_detected_name = isset($cybocoma_languages[$cybocoma_detected_language]) ? $cybocoma_languages[$cybocoma_detected_language] : 'English';
$cybocoma_string_groups = CYBOCOMA_Strings::get_string_groups();
$cybocoma_translations = CYBOCOMA_Strings::get_translations($cybocoma_effective_language);
$cybocoma_original_strings = CYBOCOMA_Strings::get_string_keys();

$cybocoma_effective_strings = $cybocoma_translations;
foreach ($cybocoma_custom_strings as $cybocoma_key => $cybocoma_value) {
	if ($cybocoma_value !== '') {
		$cybocoma_effective_strings[$cybocoma_key] = $cybocoma_value;
	}
}

$cybocoma_health = CYBOCOMA_Health::build_summary($cybocoma_enabled);
$cybocoma_snapshots = CYBOCOMA_Options::get_snapshots($cybocoma_scope_locale);
$cybocoma_updater = CYBOCOMA_Updater::get_admin_payload();
$cybocoma_update_channel_enabled = isset($cybocoma_updater['enabled']) ? (bool) $cybocoma_updater['enabled'] : true;
$cybocoma_updater_status_label = !empty($cybocoma_updater['statusLabel'])
	? (string) $cybocoma_updater['statusLabel']
	: CYBOCOMA_Updater::get_status_label(isset($cybocoma_updater['status']) ? (string) $cybocoma_updater['status'] : 'idle');
?>

<div class="cybocoma-wrap">
	<div class="cybocoma-header">
		<h1><?php esc_html_e('Cybokron Consent Manager Translations for YOOtheme Pro', 'cybokron-consent-manager-translations-yootheme'); ?></h1>
		<p><?php esc_html_e('Locale-aware consent translation management with live preview, health checks, and rollback snapshots.', 'cybokron-consent-manager-translations-yootheme'); ?></p>
	</div>

	<div id="cybocoma-message" class="cybocoma-message" aria-live="polite"></div>

	<div class="cybocoma-content">
		<form id="cybocoma-settings-form" method="post">
			<input type="hidden" id="cybocoma-settings-locale" name="settings_locale" value="<?php echo esc_attr($cybocoma_scope_locale); ?>">

			<div class="cybocoma-top-bar">
				<div class="cybocoma-select-grid">
					<div class="cybocoma-language-select">
						<label for="cybocoma-scope-locale"><?php esc_html_e('Settings Locale Scope:', 'cybokron-consent-manager-translations-yootheme'); ?></label>
						<select id="cybocoma-scope-locale" name="scope_locale">
							<?php foreach ($cybocoma_scope_locales as $cybocoma_locale_option) : ?>
								<option value="<?php echo esc_attr($cybocoma_locale_option); ?>" <?php selected($cybocoma_scope_locale, $cybocoma_locale_option); ?>>
									<?php echo esc_html(CYBOCOMA_Strings::get_locale_label($cybocoma_locale_option)); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="cybocoma-language-select">
						<label for="cybocoma-language"><?php esc_html_e('Language Preset:', 'cybokron-consent-manager-translations-yootheme'); ?></label>
						<select id="cybocoma-language" name="language">
							<?php foreach ($cybocoma_languages as $cybocoma_code => $cybocoma_name) : ?>
								<option value="<?php echo esc_attr($cybocoma_code); ?>" <?php selected($cybocoma_current_language, $cybocoma_code); ?>>
									<?php
									if ($cybocoma_code === 'auto') {
										printf('%s -> %s', esc_html($cybocoma_name), esc_html($cybocoma_detected_name));
									} else {
										echo esc_html($cybocoma_name) . ' (' . esc_html(strtoupper($cybocoma_code)) . ')';
									}
									?>
								</option>
							<?php endforeach; ?>
						</select>
						<small class="cybocoma-help-text">
							<?php
							/* translators: %s WordPress locale code */
							printf(esc_html__('Scope locale: %s', 'cybokron-consent-manager-translations-yootheme'), '<strong>' . esc_html($cybocoma_scope_locale) . '</strong>');
							?>
						</small>
					</div>
				</div>

				<div class="cybocoma-toggle">
					<label for="cybocoma-enabled"><?php esc_html_e('Enable Translations:', 'cybokron-consent-manager-translations-yootheme'); ?></label>
					<label class="cybocoma-switch">
						<input type="hidden" name="enabled" value="0">
						<input type="checkbox" id="cybocoma-enabled" name="enabled" value="1" <?php checked($cybocoma_enabled); ?>>
						<span class="cybocoma-switch-slider"></span>
					</label>
				</div>
			</div>

			<div id="cybocoma-updater-panel" class="cybocoma-updater-panel">
				<div class="cybocoma-updater-head">
					<h3><?php esc_html_e('WordPress.org Update Status', 'cybokron-consent-manager-translations-yootheme'); ?></h3>
					<div class="cybocoma-toggle">
						<label for="cybocoma-update-channel-enabled"><?php esc_html_e('Enable Periodic Checks:', 'cybokron-consent-manager-translations-yootheme'); ?></label>
						<label class="cybocoma-switch">
							<input type="hidden" name="update_channel_enabled" value="0">
							<input type="checkbox" id="cybocoma-update-channel-enabled" name="update_channel_enabled" value="1" <?php checked($cybocoma_update_channel_enabled); ?>>
							<span class="cybocoma-switch-slider"></span>
						</label>
					</div>
				</div>
				<p class="cybocoma-help-text">
					<?php esc_html_e('This setting is site-wide (not locale scoped). It refreshes WordPress.org update metadata every 12 hours.', 'cybokron-consent-manager-translations-yootheme'); ?>
				</p>
				<div class="cybocoma-updater-grid">
					<div class="cybocoma-updater-item">
						<span class="cybocoma-updater-label"><?php esc_html_e('Current Version:', 'cybokron-consent-manager-translations-yootheme'); ?></span>
						<strong id="cybocoma-updater-current-version"><?php echo esc_html(isset($cybocoma_updater['currentVersion']) ? $cybocoma_updater['currentVersion'] : CYBOCOMA_VERSION); ?></strong>
					</div>
					<div class="cybocoma-updater-item">
						<span class="cybocoma-updater-label"><?php esc_html_e('Latest Version:', 'cybokron-consent-manager-translations-yootheme'); ?></span>
						<strong id="cybocoma-updater-latest-version"><?php echo esc_html(!empty($cybocoma_updater['latestVersion']) ? $cybocoma_updater['latestVersion'] : __('Unknown', 'cybokron-consent-manager-translations-yootheme')); ?></strong>
					</div>
					<div class="cybocoma-updater-item">
						<span class="cybocoma-updater-label"><?php esc_html_e('Last Check:', 'cybokron-consent-manager-translations-yootheme'); ?></span>
						<strong id="cybocoma-updater-last-check"><?php echo esc_html(!empty($cybocoma_updater['lastCheckedAt']) ? $cybocoma_updater['lastCheckedAt'] : __('Never', 'cybokron-consent-manager-translations-yootheme')); ?></strong>
					</div>
					<div class="cybocoma-updater-item">
						<span class="cybocoma-updater-label"><?php esc_html_e('Last Status:', 'cybokron-consent-manager-translations-yootheme'); ?></span>
						<strong id="cybocoma-updater-status"><?php echo esc_html($cybocoma_updater_status_label); ?></strong>
					</div>
					<div class="cybocoma-updater-item">
						<span class="cybocoma-updater-label"><?php esc_html_e('Last Install:', 'cybokron-consent-manager-translations-yootheme'); ?></span>
						<strong id="cybocoma-updater-last-install"><?php echo esc_html(!empty($cybocoma_updater['lastInstallAt']) ? $cybocoma_updater['lastInstallAt'] : __('Never', 'cybokron-consent-manager-translations-yootheme')); ?></strong>
					</div>
					<div class="cybocoma-updater-item">
						<span class="cybocoma-updater-label"><?php esc_html_e('Last Error:', 'cybokron-consent-manager-translations-yootheme'); ?></span>
						<strong id="cybocoma-updater-last-error"><?php echo esc_html(!empty($cybocoma_updater['lastError']) ? $cybocoma_updater['lastError'] : __('None', 'cybokron-consent-manager-translations-yootheme')); ?></strong>
					</div>
				</div>
				<div class="cybocoma-updater-actions">
					<button type="button" id="cybocoma-check-update-btn" class="cybocoma-btn cybocoma-btn-secondary">
						<?php esc_html_e('Check Now', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
				</div>
			</div>

			<div id="cybocoma-health-panel" class="cybocoma-health cybocoma-health-<?php echo esc_attr($cybocoma_health['status']); ?>">
				<h3><?php esc_html_e('Compatibility Health', 'cybokron-consent-manager-translations-yootheme'); ?></h3>
				<p><?php esc_html_e('Monitors compatibility with YOOtheme consent source strings.', 'cybokron-consent-manager-translations-yootheme'); ?></p>
				<ul class="cybocoma-health-list" id="cybocoma-health-list">
					<?php if (!empty($cybocoma_health['issues'])) : ?>
						<?php foreach ($cybocoma_health['issues'] as $cybocoma_issue) : ?>
							<li class="cybocoma-health-issue"><?php echo esc_html($cybocoma_issue); ?></li>
						<?php endforeach; ?>
					<?php elseif (!empty($cybocoma_health['warnings'])) : ?>
						<?php foreach ($cybocoma_health['warnings'] as $cybocoma_warning) : ?>
							<li class="cybocoma-health-warning"><?php echo esc_html($cybocoma_warning); ?></li>
						<?php endforeach; ?>
					<?php else : ?>
						<li class="cybocoma-health-ok"><?php esc_html_e('No compatibility issues reported.', 'cybokron-consent-manager-translations-yootheme'); ?></li>
					<?php endif; ?>
				</ul>
			</div>

			<div
				class="cybocoma-tabs"
				role="tablist"
				aria-orientation="horizontal"
				aria-label="<?php esc_attr_e('Translation sections', 'cybokron-consent-manager-translations-yootheme'); ?>"
			>
				<?php foreach ($cybocoma_string_groups as $cybocoma_group_id => $cybocoma_group) :
					$cybocoma_is_active = $cybocoma_group_id === 'banner';
				?>
					<button
						type="button"
						id="cybocoma-tab-btn-<?php echo esc_attr($cybocoma_group_id); ?>"
						class="cybocoma-tab<?php echo $cybocoma_is_active ? ' active' : ''; ?>"
						data-tab="<?php echo esc_attr($cybocoma_group_id); ?>"
						role="tab"
						aria-selected="<?php echo $cybocoma_is_active ? 'true' : 'false'; ?>"
						aria-controls="cybocoma-tab-<?php echo esc_attr($cybocoma_group_id); ?>"
						tabindex="<?php echo esc_attr($cybocoma_is_active ? '0' : '-1'); ?>"
					>
						<?php echo esc_html($cybocoma_group['label']); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="cybocoma-search-bar">
				<div class="cybocoma-search-wrapper">
					<input type="text" id="cybocoma-search-strings" class="cybocoma-search-input" placeholder="<?php esc_attr_e('Search translation strings...', 'cybokron-consent-manager-translations-yootheme'); ?>">
					<button type="button" id="cybocoma-search-clear" class="cybocoma-search-clear" style="display:none;" aria-label="<?php esc_attr_e('Clear search', 'cybokron-consent-manager-translations-yootheme'); ?>">&times;</button>
				</div>
				<div class="cybocoma-stats-info">
					<div class="cybocoma-stats-bar">
						<div class="cybocoma-stats-fill" id="cybocoma-stats-bar-fill"></div>
					</div>
					<span class="cybocoma-stats-text" id="cybocoma-stats-text"></span>
				</div>
			</div>

			<div id="cybocoma-no-results" class="cybocoma-no-results" style="display:none;">
				<?php esc_html_e('No matching strings found.', 'cybokron-consent-manager-translations-yootheme'); ?>
			</div>

			<?php foreach ($cybocoma_string_groups as $cybocoma_group_id => $cybocoma_group) :
				$cybocoma_is_active = $cybocoma_group_id === 'banner';
			?>
				<div
					id="cybocoma-tab-<?php echo esc_attr($cybocoma_group_id); ?>"
					class="cybocoma-tab-content<?php echo $cybocoma_is_active ? ' active' : ''; ?>"
					role="tabpanel"
					aria-labelledby="cybocoma-tab-btn-<?php echo esc_attr($cybocoma_group_id); ?>"
					aria-hidden="<?php echo esc_attr($cybocoma_is_active ? 'false' : 'true'); ?>"
					<?php if (!$cybocoma_is_active) : ?>
						hidden
					<?php endif; ?>
				>
					<div class="cybocoma-category-header">
						<span class="cybocoma-category-icon"><?php echo esc_html(strtoupper(substr($cybocoma_group_id, 0, 1))); ?></span>
						<h3><?php echo esc_html($cybocoma_group['label']); ?> <?php esc_html_e('Strings', 'cybokron-consent-manager-translations-yootheme'); ?></h3>
					</div>

					<?php foreach ($cybocoma_group['keys'] as $cybocoma_key) :
						$cybocoma_original = isset($cybocoma_original_strings[$cybocoma_key]) ? $cybocoma_original_strings[$cybocoma_key] : '';
						$cybocoma_value = isset($cybocoma_effective_strings[$cybocoma_key]) ? $cybocoma_effective_strings[$cybocoma_key] : '';
						$cybocoma_preset = isset($cybocoma_translations[$cybocoma_key]) ? $cybocoma_translations[$cybocoma_key] : '';
						$cybocoma_has_placeholder = CYBOCOMA_Strings::has_placeholder($cybocoma_key);
						$cybocoma_is_long = strlen($cybocoma_original) > 100;
						?>
						<div class="cybocoma-string-group" data-key="<?php echo esc_attr($cybocoma_key); ?>">
							<label class="cybocoma-string-label" for="cybocoma-string-<?php echo esc_attr($cybocoma_key); ?>">
								<?php echo esc_html(CYBOCOMA_Strings::get_key_label($cybocoma_key)); ?>
							</label>

							<div class="cybocoma-original">
								<strong><?php esc_html_e('Original:', 'cybokron-consent-manager-translations-yootheme'); ?></strong><br>
								<?php echo esc_html($cybocoma_original); ?>
							</div>

							<?php if ($cybocoma_is_long) : ?>
								<textarea
									id="cybocoma-string-<?php echo esc_attr($cybocoma_key); ?>"
									name="strings[<?php echo esc_attr($cybocoma_key); ?>]"
									class="cybocoma-input cybocoma-textarea"
									data-key="<?php echo esc_attr($cybocoma_key); ?>"
									data-original-length="<?php echo esc_attr(strlen($cybocoma_original)); ?>"
									data-preset="<?php echo esc_attr($cybocoma_preset); ?>"
									placeholder="<?php esc_attr_e('Enter translation...', 'cybokron-consent-manager-translations-yootheme'); ?>"
								><?php echo esc_textarea($cybocoma_value); ?></textarea>
							<?php else : ?>
								<input
									type="text"
									id="cybocoma-string-<?php echo esc_attr($cybocoma_key); ?>"
									name="strings[<?php echo esc_attr($cybocoma_key); ?>]"
									class="cybocoma-input"
									value="<?php echo esc_attr($cybocoma_value); ?>"
									data-key="<?php echo esc_attr($cybocoma_key); ?>"
									data-original-length="<?php echo esc_attr(strlen($cybocoma_original)); ?>"
									data-preset="<?php echo esc_attr($cybocoma_preset); ?>"
									placeholder="<?php esc_attr_e('Enter translation...', 'cybokron-consent-manager-translations-yootheme'); ?>"
								>
							<?php endif; ?>

							<div class="cybocoma-field-tools">
								<button type="button" class="cybocoma-btn cybocoma-btn-link cybocoma-reset-field" data-key="<?php echo esc_attr($cybocoma_key); ?>">
									<?php esc_html_e('Reset Field', 'cybokron-consent-manager-translations-yootheme'); ?>
								</button>
								<span class="cybocoma-field-metrics" data-key="<?php echo esc_attr($cybocoma_key); ?>"></span>
							</div>

							<?php if ($cybocoma_has_placeholder) : ?>
								<span class="cybocoma-placeholder-note">
									<?php
									printf(
										/* translators: 1: %s placeholder token, 2: %1$s placeholder token. */
										esc_html__('Keep %1$s or %2$s in your translation. It will be replaced with the Privacy Policy URL.', 'cybokron-consent-manager-translations-yootheme'),
										'%s',
										'%1$s'
									);
									?>
								</span>
							<?php endif; ?>

							<div class="cybocoma-inline-feedback" data-key="<?php echo esc_attr($cybocoma_key); ?>"></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<div class="cybocoma-preview-panel" id="cybocoma-preview-panel">
				<h3><?php esc_html_e('Live Preview', 'cybokron-consent-manager-translations-yootheme'); ?></h3>
				<div class="cybocoma-preview-banner">
					<p class="cybocoma-preview-text" data-preview-key="banner_text"><?php echo esc_html(isset($cybocoma_effective_strings['banner_text']) ? $cybocoma_effective_strings['banner_text'] : ''); ?></p>
					<p class="cybocoma-preview-link" data-preview-key="banner_link"><?php echo wp_kses_post(isset($cybocoma_effective_strings['banner_link']) ? str_replace('%s', '#', $cybocoma_effective_strings['banner_link']) : ''); ?></p>
					<div class="cybocoma-preview-actions">
						<button type="button" class="cybocoma-btn cybocoma-btn-primary" disabled data-preview-key="button_accept"><?php echo esc_html(isset($cybocoma_effective_strings['button_accept']) ? $cybocoma_effective_strings['button_accept'] : ''); ?></button>
						<button type="button" class="cybocoma-btn cybocoma-btn-secondary" disabled data-preview-key="button_reject"><?php echo esc_html(isset($cybocoma_effective_strings['button_reject']) ? $cybocoma_effective_strings['button_reject'] : ''); ?></button>
						<button type="button" class="cybocoma-btn cybocoma-btn-secondary" disabled data-preview-key="button_settings"><?php echo esc_html(isset($cybocoma_effective_strings['button_settings']) ? $cybocoma_effective_strings['button_settings'] : ''); ?></button>
					</div>
				</div>
				<div class="cybocoma-preview-modal">
					<h4 data-preview-key="modal_title"><?php echo esc_html(isset($cybocoma_effective_strings['modal_title']) ? $cybocoma_effective_strings['modal_title'] : ''); ?></h4>
					<p data-preview-key="modal_content"><?php echo esc_html(isset($cybocoma_effective_strings['modal_content']) ? $cybocoma_effective_strings['modal_content'] : ''); ?></p>
					<p class="cybocoma-preview-link" data-preview-key="modal_content_link"><?php echo wp_kses_post(isset($cybocoma_effective_strings['modal_content_link']) ? str_replace('%s', '#', $cybocoma_effective_strings['modal_content_link']) : ''); ?></p>
					<div class="cybocoma-preview-actions">
						<button type="button" class="cybocoma-btn cybocoma-btn-primary" disabled data-preview-key="modal_accept"><?php echo esc_html(isset($cybocoma_effective_strings['modal_accept']) ? $cybocoma_effective_strings['modal_accept'] : ''); ?></button>
						<button type="button" class="cybocoma-btn cybocoma-btn-danger" disabled data-preview-key="modal_reject"><?php echo esc_html(isset($cybocoma_effective_strings['modal_reject']) ? $cybocoma_effective_strings['modal_reject'] : ''); ?></button>
						<button type="button" class="cybocoma-btn cybocoma-btn-secondary" disabled data-preview-key="modal_save"><?php echo esc_html(isset($cybocoma_effective_strings['modal_save']) ? $cybocoma_effective_strings['modal_save'] : ''); ?></button>
					</div>
				</div>
			</div>

			<div class="cybocoma-footer">
				<div class="cybocoma-primary-actions">
					<button type="submit" id="cybocoma-save-btn" class="cybocoma-btn cybocoma-btn-primary">
						<?php esc_html_e('Save Changes', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
					<button type="button" id="cybocoma-reset-btn" class="cybocoma-btn cybocoma-btn-danger">
						<?php esc_html_e('Reset to Default', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
				</div>

				<div class="cybocoma-secondary-actions">
					<button type="button" id="cybocoma-quality-btn" class="cybocoma-btn cybocoma-btn-secondary">
						<?php esc_html_e('Run QA Check', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
					<button type="button" id="cybocoma-health-btn" class="cybocoma-btn cybocoma-btn-secondary">
						<?php esc_html_e('Run Health Check', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
					<button type="button" id="cybocoma-import-btn" class="cybocoma-btn cybocoma-btn-secondary">
						<?php esc_html_e('Import', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
					<button type="button" id="cybocoma-export-btn" class="cybocoma-btn cybocoma-btn-secondary">
						<?php esc_html_e('Export', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
					<button type="button" id="cybocoma-copy-locale-btn" class="cybocoma-btn cybocoma-btn-secondary">
						<?php esc_html_e('Copy From Locale', 'cybokron-consent-manager-translations-yootheme'); ?>
					</button>
				</div>
			</div>

			<div class="cybocoma-snapshot-tools">
				<label for="cybocoma-snapshot-select"><?php esc_html_e('Snapshots:', 'cybokron-consent-manager-translations-yootheme'); ?></label>
				<select id="cybocoma-snapshot-select">
					<option value=""><?php esc_html_e('Select a snapshot', 'cybokron-consent-manager-translations-yootheme'); ?></option>
					<?php foreach ($cybocoma_snapshots as $cybocoma_snapshot) : ?>
						<option value="<?php echo esc_attr($cybocoma_snapshot['id']); ?>">
							<?php
							echo esc_html(
								sprintf(
									'%s - %s',
									$cybocoma_snapshot['label'],
									$cybocoma_snapshot['created_at']
								)
							);
							?>
						</option>
					<?php endforeach; ?>
				</select>
				<button type="button" id="cybocoma-restore-btn" class="cybocoma-btn cybocoma-btn-secondary">
					<?php esc_html_e('Restore Snapshot', 'cybokron-consent-manager-translations-yootheme'); ?>
				</button>
			</div>

			<div id="cybocoma-quality-report" class="cybocoma-quality-report" aria-live="polite"></div>
		</form>
	</div>
</div>

<div id="cybocoma-import-modal" class="cybocoma-modal-overlay">
	<div class="cybocoma-modal">
		<h3><?php esc_html_e('Import Settings', 'cybokron-consent-manager-translations-yootheme'); ?></h3>
		<form id="cybocoma-import-form" enctype="multipart/form-data">
			<div class="cybocoma-file-input-wrapper">
				<input type="file" id="cybocoma-import-file" accept=".json">
				<p><?php esc_html_e('Click or drag a JSON file here', 'cybokron-consent-manager-translations-yootheme'); ?></p>
				<p class="cybocoma-file-name" style="display: none;"></p>
			</div>
			<div class="cybocoma-modal-actions">
				<button type="button" class="cybocoma-btn cybocoma-btn-secondary cybocoma-modal-close">
					<?php esc_html_e('Cancel', 'cybokron-consent-manager-translations-yootheme'); ?>
				</button>
				<button type="submit" id="cybocoma-import-submit" class="cybocoma-btn cybocoma-btn-primary">
					<?php esc_html_e('Import', 'cybokron-consent-manager-translations-yootheme'); ?>
				</button>
			</div>
		</form>
	</div>
</div>

<div id="cybocoma-copy-locale-modal" class="cybocoma-modal-overlay">
	<div class="cybocoma-modal">
		<h3><?php esc_html_e('Copy Settings From Another Locale', 'cybokron-consent-manager-translations-yootheme'); ?></h3>
		<p class="cybocoma-help-text"><?php esc_html_e('Select a source locale to copy all translation settings from. This will overwrite the current locale scope settings.', 'cybokron-consent-manager-translations-yootheme'); ?></p>
		<form id="cybocoma-copy-locale-form">
			<div class="cybocoma-copy-locale-select">
				<label for="cybocoma-copy-source-locale"><?php esc_html_e('Source Locale:', 'cybokron-consent-manager-translations-yootheme'); ?></label>
				<select id="cybocoma-copy-source-locale">
					<option value=""><?php esc_html_e('Select a locale...', 'cybokron-consent-manager-translations-yootheme'); ?></option>
					<?php foreach ($cybocoma_scope_locales as $cybocoma_locale_option) : ?>
						<option value="<?php echo esc_attr($cybocoma_locale_option); ?>">
							<?php echo esc_html(CYBOCOMA_Strings::get_locale_label($cybocoma_locale_option)); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="cybocoma-modal-actions">
				<button type="button" class="cybocoma-btn cybocoma-btn-secondary cybocoma-modal-close">
					<?php esc_html_e('Cancel', 'cybokron-consent-manager-translations-yootheme'); ?>
				</button>
				<button type="submit" id="cybocoma-copy-locale-submit" class="cybocoma-btn cybocoma-btn-primary">
					<?php esc_html_e('Copy Settings', 'cybokron-consent-manager-translations-yootheme'); ?>
				</button>
			</div>
		</form>
	</div>
</div>
