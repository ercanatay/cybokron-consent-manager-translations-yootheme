<?php
/**
 * Admin Settings Page Template
 *
 * @package YT_Consent_Translations
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!current_user_can('manage_options')) {
	return;
}

$ytct_scope_locale = YTCT_Options::normalize_locale(get_locale());
$ytct_scope_locale_input = filter_input(INPUT_GET, 'scope_locale', FILTER_DEFAULT);
if (is_scalar($ytct_scope_locale_input) && $ytct_scope_locale_input !== '') {
	$ytct_scope_locale = YTCT_Options::normalize_locale(sanitize_text_field(wp_unslash((string) $ytct_scope_locale_input)));
}

$ytct_options = YTCT_Options::get_options($ytct_scope_locale);
$ytct_enabled = isset($ytct_options['enabled']) ? (bool) $ytct_options['enabled'] : true;
$ytct_current_language = isset($ytct_options['language']) ? $ytct_options['language'] : 'en';
$ytct_custom_strings = isset($ytct_options['custom_strings']) && is_array($ytct_options['custom_strings']) ? $ytct_options['custom_strings'] : [];

$ytct_languages = YTCT_Strings::get_languages();
if (!isset($ytct_languages[$ytct_current_language])) {
	$ytct_current_language = 'en';
}

$ytct_scope_locales = YTCT_Strings::get_supported_wp_locales();
$ytct_scope_lookup = array_fill_keys($ytct_scope_locales, true);
if (!isset($ytct_scope_lookup[$ytct_scope_locale])) {
	$ytct_scope_locales[] = $ytct_scope_locale;
	sort($ytct_scope_locales);
}

$ytct_detected_language = YTCT_Strings::detect_language_from_locale($ytct_scope_locale);
$ytct_effective_language = $ytct_current_language === 'auto'
	? $ytct_detected_language
	: YTCT_Strings::resolve_language_code($ytct_current_language, false);
$ytct_detected_name = isset($ytct_languages[$ytct_detected_language]) ? $ytct_languages[$ytct_detected_language] : 'English';
$ytct_string_groups = YTCT_Strings::get_string_groups();
$ytct_translations = YTCT_Strings::get_translations($ytct_effective_language);
$ytct_original_strings = YTCT_Strings::get_string_keys();

$ytct_effective_strings = $ytct_translations;
foreach ($ytct_custom_strings as $ytct_key => $ytct_value) {
	if ($ytct_value !== '') {
		$ytct_effective_strings[$ytct_key] = $ytct_value;
	}
}

$ytct_health = YTCT_Health::build_summary($ytct_enabled);
$ytct_snapshots = YTCT_Options::get_snapshots($ytct_scope_locale);
$ytct_updater = YTCT_Updater::get_admin_payload();
$ytct_update_channel_enabled = isset($ytct_updater['enabled']) ? (bool) $ytct_updater['enabled'] : true;
?>

<div class="ytct-wrap">
	<div class="ytct-header">
		<h1><?php esc_html_e('YT Consent Translations', 'yt-consent-translations-1.3.5'); ?></h1>
		<p><?php esc_html_e('Locale-aware consent translation management with live preview, health checks, and rollback snapshots.', 'yt-consent-translations-1.3.5'); ?></p>
	</div>

	<div id="ytct-message" class="ytct-message" aria-live="polite"></div>

	<div class="ytct-content">
		<form id="ytct-settings-form" method="post">
			<input type="hidden" id="ytct-settings-locale" name="settings_locale" value="<?php echo esc_attr($ytct_scope_locale); ?>">

			<div class="ytct-top-bar">
				<div class="ytct-select-grid">
					<div class="ytct-language-select">
						<label for="ytct-scope-locale"><?php esc_html_e('Settings Locale Scope:', 'yt-consent-translations-1.3.5'); ?></label>
						<select id="ytct-scope-locale" name="scope_locale">
							<?php foreach ($ytct_scope_locales as $ytct_locale_option) : ?>
								<option value="<?php echo esc_attr($ytct_locale_option); ?>" <?php selected($ytct_scope_locale, $ytct_locale_option); ?>>
									<?php echo esc_html(YTCT_Strings::get_locale_label($ytct_locale_option)); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="ytct-language-select">
						<label for="ytct-language"><?php esc_html_e('Language Preset:', 'yt-consent-translations-1.3.5'); ?></label>
						<select id="ytct-language" name="language">
							<?php foreach ($ytct_languages as $ytct_code => $ytct_name) : ?>
								<option value="<?php echo esc_attr($ytct_code); ?>" <?php selected($ytct_current_language, $ytct_code); ?>>
									<?php
									if ($ytct_code === 'auto') {
										printf('%s -> %s', esc_html($ytct_name), esc_html($ytct_detected_name));
									} else {
										echo esc_html($ytct_name) . ' (' . esc_html(strtoupper($ytct_code)) . ')';
									}
									?>
								</option>
							<?php endforeach; ?>
						</select>
						<small class="ytct-help-text">
							<?php
							/* translators: %s WordPress locale code */
							printf(esc_html__('Scope locale: %s', 'yt-consent-translations-1.3.5'), '<strong>' . esc_html($ytct_scope_locale) . '</strong>');
							?>
						</small>
					</div>
				</div>

				<div class="ytct-toggle">
					<label for="ytct-enabled"><?php esc_html_e('Enable Translations:', 'yt-consent-translations-1.3.5'); ?></label>
					<label class="ytct-switch">
						<input type="hidden" name="enabled" value="0">
						<input type="checkbox" id="ytct-enabled" name="enabled" value="1" <?php checked($ytct_enabled); ?>>
						<span class="ytct-switch-slider"></span>
					</label>
				</div>
			</div>

			<div id="ytct-updater-panel" class="ytct-updater-panel">
				<div class="ytct-updater-head">
					<h3><?php esc_html_e('GitHub Stable Auto Update', 'yt-consent-translations-1.3.5'); ?></h3>
					<div class="ytct-toggle">
						<label for="ytct-update-channel-enabled"><?php esc_html_e('Enable Channel:', 'yt-consent-translations-1.3.5'); ?></label>
						<label class="ytct-switch">
							<input type="hidden" name="update_channel_enabled" value="0">
							<input type="checkbox" id="ytct-update-channel-enabled" name="update_channel_enabled" value="1" <?php checked($ytct_update_channel_enabled); ?>>
							<span class="ytct-switch-slider"></span>
						</label>
					</div>
				</div>
				<p class="ytct-help-text">
					<?php esc_html_e('This updater setting is site-wide (not locale scoped). Channel: stable, interval: every 12 hours.', 'yt-consent-translations-1.3.5'); ?>
				</p>
				<div class="ytct-updater-grid">
					<div class="ytct-updater-item">
						<span class="ytct-updater-label"><?php esc_html_e('Current Version:', 'yt-consent-translations-1.3.5'); ?></span>
						<strong id="ytct-updater-current-version"><?php echo esc_html(isset($ytct_updater['currentVersion']) ? $ytct_updater['currentVersion'] : YTCT_VERSION); ?></strong>
					</div>
					<div class="ytct-updater-item">
						<span class="ytct-updater-label"><?php esc_html_e('Latest Version:', 'yt-consent-translations-1.3.5'); ?></span>
						<strong id="ytct-updater-latest-version"><?php echo esc_html(!empty($ytct_updater['latestVersion']) ? $ytct_updater['latestVersion'] : __('Unknown', 'yt-consent-translations-1.3.5')); ?></strong>
					</div>
					<div class="ytct-updater-item">
						<span class="ytct-updater-label"><?php esc_html_e('Last Check:', 'yt-consent-translations-1.3.5'); ?></span>
						<strong id="ytct-updater-last-check"><?php echo esc_html(!empty($ytct_updater['lastCheckedAt']) ? $ytct_updater['lastCheckedAt'] : __('Never', 'yt-consent-translations-1.3.5')); ?></strong>
					</div>
					<div class="ytct-updater-item">
						<span class="ytct-updater-label"><?php esc_html_e('Last Status:', 'yt-consent-translations-1.3.5'); ?></span>
						<strong id="ytct-updater-status"><?php echo esc_html(!empty($ytct_updater['status']) ? $ytct_updater['status'] : __('idle', 'yt-consent-translations-1.3.5')); ?></strong>
					</div>
					<div class="ytct-updater-item">
						<span class="ytct-updater-label"><?php esc_html_e('Last Install:', 'yt-consent-translations-1.3.5'); ?></span>
						<strong id="ytct-updater-last-install"><?php echo esc_html(!empty($ytct_updater['lastInstallAt']) ? $ytct_updater['lastInstallAt'] : __('Never', 'yt-consent-translations-1.3.5')); ?></strong>
					</div>
					<div class="ytct-updater-item">
						<span class="ytct-updater-label"><?php esc_html_e('Last Error:', 'yt-consent-translations-1.3.5'); ?></span>
						<strong id="ytct-updater-last-error"><?php echo esc_html(!empty($ytct_updater['lastError']) ? $ytct_updater['lastError'] : __('None', 'yt-consent-translations-1.3.5')); ?></strong>
					</div>
				</div>
				<div class="ytct-updater-actions">
					<button type="button" id="ytct-check-update-btn" class="ytct-btn ytct-btn-secondary">
						<?php esc_html_e('Check Now', 'yt-consent-translations-1.3.5'); ?>
					</button>
				</div>
			</div>

			<div id="ytct-health-panel" class="ytct-health ytct-health-<?php echo esc_attr($ytct_health['status']); ?>">
				<h3><?php esc_html_e('Compatibility Health', 'yt-consent-translations-1.3.5'); ?></h3>
				<p><?php esc_html_e('Monitors compatibility with YOOtheme consent source strings.', 'yt-consent-translations-1.3.5'); ?></p>
				<ul class="ytct-health-list" id="ytct-health-list">
					<?php if (!empty($ytct_health['issues'])) : ?>
						<?php foreach ($ytct_health['issues'] as $ytct_issue) : ?>
							<li class="ytct-health-issue"><?php echo esc_html($ytct_issue); ?></li>
						<?php endforeach; ?>
					<?php elseif (!empty($ytct_health['warnings'])) : ?>
						<?php foreach ($ytct_health['warnings'] as $ytct_warning) : ?>
							<li class="ytct-health-warning"><?php echo esc_html($ytct_warning); ?></li>
						<?php endforeach; ?>
					<?php else : ?>
						<li class="ytct-health-ok"><?php esc_html_e('No compatibility issues reported.', 'yt-consent-translations-1.3.5'); ?></li>
					<?php endif; ?>
				</ul>
			</div>

			<div class="ytct-tabs">
				<?php foreach ($ytct_string_groups as $ytct_group_id => $ytct_group) : ?>
					<button type="button" class="ytct-tab<?php echo $ytct_group_id === 'banner' ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($ytct_group_id); ?>">
						<?php echo esc_html($ytct_group['label']); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php foreach ($ytct_string_groups as $ytct_group_id => $ytct_group) : ?>
				<div id="ytct-tab-<?php echo esc_attr($ytct_group_id); ?>" class="ytct-tab-content<?php echo $ytct_group_id === 'banner' ? ' active' : ''; ?>">
					<div class="ytct-category-header">
						<span class="ytct-category-icon"><?php echo esc_html(strtoupper(substr($ytct_group_id, 0, 1))); ?></span>
						<h3><?php echo esc_html($ytct_group['label']); ?> <?php esc_html_e('Strings', 'yt-consent-translations-1.3.5'); ?></h3>
					</div>

					<?php foreach ($ytct_group['keys'] as $ytct_key) :
						$ytct_original = isset($ytct_original_strings[$ytct_key]) ? $ytct_original_strings[$ytct_key] : '';
						$ytct_value = isset($ytct_effective_strings[$ytct_key]) ? $ytct_effective_strings[$ytct_key] : '';
						$ytct_preset = isset($ytct_translations[$ytct_key]) ? $ytct_translations[$ytct_key] : '';
						$ytct_has_placeholder = YTCT_Strings::has_placeholder($ytct_key);
						$ytct_is_long = strlen($ytct_original) > 100;
						?>
						<div class="ytct-string-group" data-key="<?php echo esc_attr($ytct_key); ?>">
							<label class="ytct-string-label" for="ytct-string-<?php echo esc_attr($ytct_key); ?>">
								<?php echo esc_html(YTCT_Strings::get_key_label($ytct_key)); ?>
							</label>

							<div class="ytct-original">
								<strong><?php esc_html_e('Original:', 'yt-consent-translations-1.3.5'); ?></strong><br>
								<?php echo esc_html($ytct_original); ?>
							</div>

							<?php if ($ytct_is_long) : ?>
								<textarea
									id="ytct-string-<?php echo esc_attr($ytct_key); ?>"
									name="strings[<?php echo esc_attr($ytct_key); ?>]"
									class="ytct-input ytct-textarea"
									data-key="<?php echo esc_attr($ytct_key); ?>"
									data-original-length="<?php echo esc_attr(strlen($ytct_original)); ?>"
									data-preset="<?php echo esc_attr($ytct_preset); ?>"
									placeholder="<?php esc_attr_e('Enter translation...', 'yt-consent-translations-1.3.5'); ?>"
								><?php echo esc_textarea($ytct_value); ?></textarea>
							<?php else : ?>
								<input
									type="text"
									id="ytct-string-<?php echo esc_attr($ytct_key); ?>"
									name="strings[<?php echo esc_attr($ytct_key); ?>]"
									class="ytct-input"
									value="<?php echo esc_attr($ytct_value); ?>"
									data-key="<?php echo esc_attr($ytct_key); ?>"
									data-original-length="<?php echo esc_attr(strlen($ytct_original)); ?>"
									data-preset="<?php echo esc_attr($ytct_preset); ?>"
									placeholder="<?php esc_attr_e('Enter translation...', 'yt-consent-translations-1.3.5'); ?>"
								>
							<?php endif; ?>

							<div class="ytct-field-tools">
								<button type="button" class="ytct-btn ytct-btn-link ytct-reset-field" data-key="<?php echo esc_attr($ytct_key); ?>">
									<?php esc_html_e('Reset Field', 'yt-consent-translations-1.3.5'); ?>
								</button>
								<span class="ytct-field-metrics" data-key="<?php echo esc_attr($ytct_key); ?>"></span>
							</div>

							<?php if ($ytct_has_placeholder) : ?>
								<span class="ytct-placeholder-note">
									<?php
									/* translators: 1: %s placeholder token, 2: %1$s placeholder token. */
									printf(
										esc_html__('Keep %1$s or %2$s in your translation. It will be replaced with the Privacy Policy URL.', 'yt-consent-translations-1.3.5'),
										'%s',
										'%1$s'
									);
									?>
								</span>
							<?php endif; ?>

							<div class="ytct-inline-feedback" data-key="<?php echo esc_attr($ytct_key); ?>"></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<div class="ytct-preview-panel" id="ytct-preview-panel">
				<h3><?php esc_html_e('Live Preview', 'yt-consent-translations-1.3.5'); ?></h3>
				<div class="ytct-preview-banner">
					<p class="ytct-preview-text" data-preview-key="banner_text"><?php echo esc_html(isset($ytct_effective_strings['banner_text']) ? $ytct_effective_strings['banner_text'] : ''); ?></p>
					<p class="ytct-preview-link" data-preview-key="banner_link"><?php echo wp_kses_post(isset($ytct_effective_strings['banner_link']) ? str_replace('%s', '#', $ytct_effective_strings['banner_link']) : ''); ?></p>
					<div class="ytct-preview-actions">
						<button type="button" class="ytct-btn ytct-btn-primary" disabled data-preview-key="button_accept"><?php echo esc_html(isset($ytct_effective_strings['button_accept']) ? $ytct_effective_strings['button_accept'] : ''); ?></button>
						<button type="button" class="ytct-btn ytct-btn-secondary" disabled data-preview-key="button_reject"><?php echo esc_html(isset($ytct_effective_strings['button_reject']) ? $ytct_effective_strings['button_reject'] : ''); ?></button>
						<button type="button" class="ytct-btn ytct-btn-secondary" disabled data-preview-key="button_settings"><?php echo esc_html(isset($ytct_effective_strings['button_settings']) ? $ytct_effective_strings['button_settings'] : ''); ?></button>
					</div>
				</div>
				<div class="ytct-preview-modal">
					<h4 data-preview-key="modal_title"><?php echo esc_html(isset($ytct_effective_strings['modal_title']) ? $ytct_effective_strings['modal_title'] : ''); ?></h4>
					<p data-preview-key="modal_content"><?php echo esc_html(isset($ytct_effective_strings['modal_content']) ? $ytct_effective_strings['modal_content'] : ''); ?></p>
					<p class="ytct-preview-link" data-preview-key="modal_content_link"><?php echo wp_kses_post(isset($ytct_effective_strings['modal_content_link']) ? str_replace('%s', '#', $ytct_effective_strings['modal_content_link']) : ''); ?></p>
					<div class="ytct-preview-actions">
						<button type="button" class="ytct-btn ytct-btn-primary" disabled data-preview-key="modal_accept"><?php echo esc_html(isset($ytct_effective_strings['modal_accept']) ? $ytct_effective_strings['modal_accept'] : ''); ?></button>
						<button type="button" class="ytct-btn ytct-btn-danger" disabled data-preview-key="modal_reject"><?php echo esc_html(isset($ytct_effective_strings['modal_reject']) ? $ytct_effective_strings['modal_reject'] : ''); ?></button>
						<button type="button" class="ytct-btn ytct-btn-secondary" disabled data-preview-key="modal_save"><?php echo esc_html(isset($ytct_effective_strings['modal_save']) ? $ytct_effective_strings['modal_save'] : ''); ?></button>
					</div>
				</div>
			</div>

			<div class="ytct-footer">
				<div class="ytct-primary-actions">
					<button type="submit" id="ytct-save-btn" class="ytct-btn ytct-btn-primary">
						<?php esc_html_e('Save Changes', 'yt-consent-translations-1.3.5'); ?>
					</button>
					<button type="button" id="ytct-reset-btn" class="ytct-btn ytct-btn-danger">
						<?php esc_html_e('Reset to Default', 'yt-consent-translations-1.3.5'); ?>
					</button>
				</div>

				<div class="ytct-secondary-actions">
					<button type="button" id="ytct-quality-btn" class="ytct-btn ytct-btn-secondary">
						<?php esc_html_e('Run QA Check', 'yt-consent-translations-1.3.5'); ?>
					</button>
					<button type="button" id="ytct-health-btn" class="ytct-btn ytct-btn-secondary">
						<?php esc_html_e('Run Health Check', 'yt-consent-translations-1.3.5'); ?>
					</button>
					<button type="button" id="ytct-import-btn" class="ytct-btn ytct-btn-secondary">
						<?php esc_html_e('Import', 'yt-consent-translations-1.3.5'); ?>
					</button>
					<button type="button" id="ytct-export-btn" class="ytct-btn ytct-btn-secondary">
						<?php esc_html_e('Export', 'yt-consent-translations-1.3.5'); ?>
					</button>
				</div>
			</div>

			<div class="ytct-snapshot-tools">
				<label for="ytct-snapshot-select"><?php esc_html_e('Snapshots:', 'yt-consent-translations-1.3.5'); ?></label>
				<select id="ytct-snapshot-select">
					<option value=""><?php esc_html_e('Select a snapshot', 'yt-consent-translations-1.3.5'); ?></option>
					<?php foreach ($ytct_snapshots as $ytct_snapshot) : ?>
						<option value="<?php echo esc_attr($ytct_snapshot['id']); ?>">
							<?php
							echo esc_html(
								sprintf(
									'%s - %s',
									$ytct_snapshot['label'],
									$ytct_snapshot['created_at']
								)
							);
							?>
						</option>
					<?php endforeach; ?>
				</select>
				<button type="button" id="ytct-restore-btn" class="ytct-btn ytct-btn-secondary">
					<?php esc_html_e('Restore Snapshot', 'yt-consent-translations-1.3.5'); ?>
				</button>
			</div>

			<div id="ytct-quality-report" class="ytct-quality-report" aria-live="polite"></div>
		</form>
	</div>
</div>

<div id="ytct-import-modal" class="ytct-modal-overlay">
	<div class="ytct-modal">
		<h3><?php esc_html_e('Import Settings', 'yt-consent-translations-1.3.5'); ?></h3>
		<form id="ytct-import-form" enctype="multipart/form-data">
			<div class="ytct-file-input-wrapper">
				<input type="file" id="ytct-import-file" accept=".json">
				<p><?php esc_html_e('Click or drag a JSON file here', 'yt-consent-translations-1.3.5'); ?></p>
				<p class="ytct-file-name" style="display: none;"></p>
			</div>
			<div class="ytct-modal-actions">
				<button type="button" class="ytct-btn ytct-btn-secondary ytct-modal-close">
					<?php esc_html_e('Cancel', 'yt-consent-translations-1.3.5'); ?>
				</button>
				<button type="submit" id="ytct-import-submit" class="ytct-btn ytct-btn-primary">
					<?php esc_html_e('Import', 'yt-consent-translations-1.3.5'); ?>
				</button>
			</div>
		</form>
	</div>
</div>
