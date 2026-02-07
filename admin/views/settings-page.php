<?php
/**
 * Admin Settings Page Template
 *
 * @package YT_Consent_Translations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

// Defense-in-depth: verify user capabilities
if (!current_user_can('manage_options')) {
	return;
}

// Get current options
$ytct_options = get_option(YTCT_OPTION_NAME, [
	'enabled' => true,
	'language' => 'en',
	'custom_strings' => []
]);

$ytct_enabled = isset($ytct_options['enabled']) ? (bool) $ytct_options['enabled'] : true;
$ytct_current_language = isset($ytct_options['language']) ? $ytct_options['language'] : 'en';
$ytct_custom_strings = isset($ytct_options['custom_strings']) ? $ytct_options['custom_strings'] : [];

// Get available data
$ytct_languages = YTCT_Strings::get_languages();
if (!isset($ytct_languages[$ytct_current_language])) {
	$ytct_current_language = 'en';
}

$ytct_detected_language = YTCT_Strings::resolve_language_code('auto', true);
$ytct_effective_language = $ytct_current_language === 'auto'
	? $ytct_detected_language
	: YTCT_Strings::resolve_language_code($ytct_current_language, false);
$ytct_detected_name = isset($ytct_languages[$ytct_detected_language]) ? $ytct_languages[$ytct_detected_language] : 'English';
$ytct_string_groups = YTCT_Strings::get_string_groups();
$ytct_translations = YTCT_Strings::get_translations($ytct_effective_language);
$ytct_original_strings = YTCT_Strings::get_string_keys();
?>

<div class="ytct-wrap">
	<!-- Header -->
	<div class="ytct-header">
		<h1><?php esc_html_e('YT Consent Translations', 'yt-consent-translations'); ?></h1>
		<p><?php esc_html_e('Translate YOOtheme Pro 5 Consent Manager texts easily from your WordPress admin panel.', 'yt-consent-translations'); ?></p>
	</div>

	<!-- Message Area -->
	<div id="ytct-message" class="ytct-message"></div>

	<!-- Main Content -->
	<div class="ytct-content">
		<form id="ytct-settings-form" method="post">
			<!-- Top Bar -->
			<div class="ytct-top-bar">
				<div class="ytct-language-select">
					<label for="ytct-language"><?php esc_html_e('Language Preset:', 'yt-consent-translations'); ?></label>
						<select id="ytct-language" name="language">
						    <?php foreach ($ytct_languages as $ytct_code => $ytct_name) : ?>
						        <option value="<?php echo esc_attr($ytct_code); ?>" <?php selected($ytct_current_language, $ytct_code); ?>>
						            <?php 
						            if ($ytct_code === 'auto') {
						                printf('%s → %s', esc_html($ytct_name), esc_html($ytct_detected_name));
						            } else {
						                echo esc_html($ytct_name) . ' (' . esc_html(strtoupper($ytct_code)) . ')';
						            }
					            ?>
					        </option>
					    <?php endforeach; ?>
					</select>
					<?php if ($ytct_current_language === 'auto') : ?>
					    <small style="display: block; margin-top: 5px; color: #666;">
					        <?php 
					        /* translators: %s is the WordPress locale code */
					        echo esc_html__('WordPress language:', 'yt-consent-translations');
					        echo ' <strong>' . esc_html(get_locale()) . '</strong>';
					        ?>
					    </small>
					<?php endif; ?>
				</div>

				<div class="ytct-toggle">
					<label for="ytct-enabled"><?php esc_html_e('Enable Translations:', 'yt-consent-translations'); ?></label>
					<label class="ytct-switch">
					    <!-- Hidden input ensures "0" is sent when checkbox is unchecked -->
					    <input type="hidden" name="enabled" value="0">
					    <input type="checkbox" id="ytct-enabled" name="enabled" value="1" <?php checked($ytct_enabled); ?>>
					    <span class="ytct-switch-slider"></span>
					</label>
				</div>
			</div>

			<!-- Tabs -->
			<div class="ytct-tabs">
				<?php foreach ($ytct_string_groups as $ytct_group_id => $ytct_group) : ?>
					<button type="button" class="ytct-tab<?php echo $ytct_group_id === 'banner' ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($ytct_group_id); ?>">
					    <?php echo esc_html($ytct_group['label']); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<!-- Tab Contents -->
			<?php foreach ($ytct_string_groups as $ytct_group_id => $ytct_group) : ?>
				<div id="ytct-tab-<?php echo esc_attr($ytct_group_id); ?>" class="ytct-tab-content<?php echo $ytct_group_id === 'banner' ? ' active' : ''; ?>">
					<div class="ytct-category-header">
					    <span class="ytct-category-icon">
					        <?php
					        $ytct_icons = [
					            'banner' => '🍪',
					            'modal' => '⚙️',
					            'categories' => '📂',
					            'buttons' => '🔘'
					        ];
					        echo esc_html(isset($ytct_icons[$ytct_group_id]) ? $ytct_icons[$ytct_group_id] : '📝');
					        ?>
					    </span>
					    <h3><?php echo esc_html($ytct_group['label']); ?> <?php esc_html_e('Strings', 'yt-consent-translations'); ?></h3>
					</div>

					<?php foreach ($ytct_group['keys'] as $ytct_key) : 
					    $ytct_original = isset($ytct_original_strings[$ytct_key]) ? $ytct_original_strings[$ytct_key] : '';
					    $ytct_value = isset($ytct_custom_strings[$ytct_key]) && !empty($ytct_custom_strings[$ytct_key]) 
					        ? $ytct_custom_strings[$ytct_key] 
					        : (isset($ytct_translations[$ytct_key]) ? $ytct_translations[$ytct_key] : '');
					    $ytct_has_placeholder = YTCT_Strings::has_placeholder($ytct_key);
					    $ytct_is_long = strlen($ytct_original) > 100;
					?>
					    <div class="ytct-string-group">
					        <label class="ytct-string-label" for="ytct-string-<?php echo esc_attr($ytct_key); ?>">
					            <?php echo esc_html(YTCT_Strings::get_key_label($ytct_key)); ?>
					        </label>
					        
					        <div class="ytct-original">
					            <strong><?php esc_html_e('Original:', 'yt-consent-translations'); ?></strong><br>
					            <?php echo esc_html($ytct_original); ?>
					        </div>

					        <?php if ($ytct_is_long) : ?>
					            <textarea 
					                id="ytct-string-<?php echo esc_attr($ytct_key); ?>"
					                name="strings[<?php echo esc_attr($ytct_key); ?>]"
					                class="ytct-input ytct-textarea"
					                placeholder="<?php esc_attr_e('Enter translation...', 'yt-consent-translations'); ?>"
					            ><?php echo esc_textarea($ytct_value); ?></textarea>
					        <?php else : ?>
					            <input 
					                type="text"
					                id="ytct-string-<?php echo esc_attr($ytct_key); ?>"
					                name="strings[<?php echo esc_attr($ytct_key); ?>]"
					                class="ytct-input"
					                value="<?php echo esc_attr($ytct_value); ?>"
					                placeholder="<?php esc_attr_e('Enter translation...', 'yt-consent-translations'); ?>"
					            >
					        <?php endif; ?>

					        <?php if ($ytct_has_placeholder) : ?>
					            <span class="ytct-placeholder-note">
					                ⚠️ <?php esc_html_e('Keep', 'yt-consent-translations'); ?> <code>%s</code> <?php esc_html_e('in your translation - it will be replaced with the Privacy Policy URL.', 'yt-consent-translations'); ?>
					            </span>
					        <?php endif; ?>
					    </div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<!-- Footer Actions -->
			<div class="ytct-footer">
				<div class="ytct-primary-actions">
					<button type="submit" id="ytct-save-btn" class="ytct-btn ytct-btn-primary">
					    <?php esc_html_e('Save Changes', 'yt-consent-translations'); ?>
					</button>
					<button type="button" id="ytct-reset-btn" class="ytct-btn ytct-btn-danger">
					    <?php esc_html_e('Reset to Default', 'yt-consent-translations'); ?>
					</button>
				</div>
				
				<div class="ytct-secondary-actions">
					<button type="button" id="ytct-import-btn" class="ytct-btn ytct-btn-secondary">
					    <?php esc_html_e('Import', 'yt-consent-translations'); ?>
					</button>
					<button type="button" id="ytct-export-btn" class="ytct-btn ytct-btn-secondary">
					    <?php esc_html_e('Export', 'yt-consent-translations'); ?>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Import Modal -->
<div id="ytct-import-modal" class="ytct-modal-overlay">
	<div class="ytct-modal">
		<h3><?php esc_html_e('Import Settings', 'yt-consent-translations'); ?></h3>
		<form id="ytct-import-form" enctype="multipart/form-data">
			<div class="ytct-file-input-wrapper">
				<input type="file" id="ytct-import-file" accept=".json">
				<p><?php esc_html_e('Click or drag a JSON file here', 'yt-consent-translations'); ?></p>
				<p class="ytct-file-name" style="display: none;"></p>
			</div>
			<div class="ytct-modal-actions">
				<button type="button" class="ytct-btn ytct-btn-secondary ytct-modal-close">
					<?php esc_html_e('Cancel', 'yt-consent-translations'); ?>
				</button>
				<button type="submit" id="ytct-import-submit" class="ytct-btn ytct-btn-primary">
					<?php esc_html_e('Import', 'yt-consent-translations'); ?>
				</button>
			</div>
		</form>
	</div>
</div>
