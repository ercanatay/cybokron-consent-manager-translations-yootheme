/**
 * YT Consent Translations - Admin Script
 *
 * @package YT_Consent_Translations
 */

(function($) {
    'use strict';

    var $form = $('#ytct-settings-form');
    var $saveBtn = $('#ytct-save-btn');
    var $resetBtn = $('#ytct-reset-btn');
    var $exportBtn = $('#ytct-export-btn');
    var $importBtn = $('#ytct-import-btn');
    var $qualityBtn = $('#ytct-quality-btn');
    var $healthBtn = $('#ytct-health-btn');
    var $restoreBtn = $('#ytct-restore-btn');
    var $languageSelect = $('#ytct-language');
    var $scopeSelect = $('#ytct-scope-locale');
    var $scopeHidden = $('#ytct-settings-locale');
    var $message = $('#ytct-message');
    var $tabs = $('.ytct-tab');
    var $tabContents = $('.ytct-tab-content');
    var $modal = $('#ytct-import-modal');
    var $qualityReport = $('#ytct-quality-report');

    var state = {
        initialHash: '',
        isDirty: false
    };

    function init() {
        bindEvents();
        initTabs();
        initializePresetValues();
        refreshAllUiState();
        captureInitialState();
    }

    function bindEvents() {
        $form.on('submit', function(e) {
            e.preventDefault();
            saveSettings();
        });

        $resetBtn.on('click', function(e) {
            e.preventDefault();
            if (window.confirm(ytctAdmin.strings.confirmReset)) {
                resetSettings();
            }
        });

        $exportBtn.on('click', function(e) {
            e.preventDefault();
            exportSettings();
        });

        $importBtn.on('click', function(e) {
            e.preventDefault();
            showImportModal();
        });

        $qualityBtn.on('click', function(e) {
            e.preventDefault();
            runQualityCheck();
        });

        $healthBtn.on('click', function(e) {
            e.preventDefault();
            runHealthCheck();
        });

        $restoreBtn.on('click', function(e) {
            e.preventDefault();
            restoreSnapshot();
        });

        $scopeSelect.on('change', function() {
            var locale = $(this).val();
            loadScope(locale);
        });

        $languageSelect.on('change', function() {
            loadLanguagePreset($(this).val());
        });

        $tabs.on('click', function() {
            switchTab($(this).data('tab'));
        });

        $('.ytct-modal-close, .ytct-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                hideImportModal();
            }
        });

        $('#ytct-import-form').on('submit', function(e) {
            e.preventDefault();
            importSettings();
        });

        $('#ytct-import-file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).siblings('.ytct-file-name').text(fileName).show();
            }
        });

        $form.on('input', 'input[name^="strings["], textarea[name^="strings["]', function() {
            var $input = $(this);
            var key = $input.data('key');
            validateField(key, $input);
            updatePreviewForKey(key, $input.val());
            updateFieldMetrics(key, $input);
            markDirtyIfNeeded();
        });

        $form.on('click', '.ytct-reset-field', function(e) {
            e.preventDefault();
            var key = $(this).data('key');
            resetFieldToPreset(key);
            markDirtyIfNeeded();
        });

        $(window).on('beforeunload', function(e) {
            if (!state.isDirty) {
                return;
            }

            e.preventDefault();
            e.returnValue = ytctAdmin.strings.unsavedChanges;
            return ytctAdmin.strings.unsavedChanges;
        });
    }

    function initializePresetValues() {
        $form.find('input[name^="strings["], textarea[name^="strings["]').each(function() {
            var $input = $(this);
            if ($input.attr('data-preset') === undefined) {
                $input.attr('data-preset', $input.val() || '');
            }
        });
    }

    function initTabs() {
        var activeTab = $tabs.filter('.active').data('tab') || 'banner';
        switchTab(activeTab);
    }

    function switchTab(tabId) {
        var validTabs = ['banner', 'modal', 'categories', 'buttons'];
        if (validTabs.indexOf(tabId) === -1) {
            tabId = 'banner';
        }

        $tabs.removeClass('active');
        $tabs.filter('[data-tab="' + tabId + '"]').addClass('active');

        $tabContents.removeClass('active');
        $('#ytct-tab-' + tabId).addClass('active');
    }

    function getScopeLocale() {
        return $scopeHidden.val() || $scopeSelect.val() || '';
    }

    function serializeFormState() {
        var payload = {
            enabled: $('#ytct-enabled').is(':checked'),
            language: $languageSelect.val(),
            settings_locale: getScopeLocale(),
            strings: {}
        };

        $form.find('input[name^="strings["], textarea[name^="strings["]').each(function() {
            var $input = $(this);
            payload.strings[$input.data('key')] = $input.val();
        });

        return JSON.stringify(payload);
    }

    function captureInitialState() {
        state.initialHash = serializeFormState();
        state.isDirty = false;
    }

    function markDirtyIfNeeded() {
        state.isDirty = serializeFormState() !== state.initialHash;
    }

    function refreshAllUiState() {
        validateAllFields();
        refreshPreview();
        refreshFieldMetrics();
    }

    function refreshPreview() {
        $form.find('input[name^="strings["], textarea[name^="strings["]').each(function() {
            var $input = $(this);
            updatePreviewForKey($input.data('key'), $input.val());
        });
    }

    function updatePreviewForKey(key, value) {
        if (!key) {
            return;
        }

        var $targets = $('[data-preview-key="' + key + '"]');
        if (!$targets.length) {
            return;
        }

        if (key === 'banner_link' || key === 'modal_content_link') {
            var htmlValue = (value || '').replace(/%1\$s|%s/g, '#');
            $targets.html(htmlValue);
            return;
        }

        $targets.text(value || '');
    }

    function validateAllFields() {
        $form.find('input[name^="strings["], textarea[name^="strings["]').each(function() {
            var $input = $(this);
            validateField($input.data('key'), $input);
        });
    }

    function validateField(key, $input) {
        if (!key || !$input || !$input.length) {
            return;
        }

        var value = ($input.val() || '').trim();
        var preset = ($input.attr('data-preset') || '').trim();
        var originalLength = parseInt($input.attr('data-original-length'), 10) || 0;
        var issues = [];
        var warnings = [];

        if ((key === 'banner_link' || key === 'modal_content_link') && value !== '') {
            if (value.indexOf('%s') === -1 && value.indexOf('%1$s') === -1) {
                issues.push('Missing required placeholder (%s or %1$s).');
            }
        }

        if (value !== '' && value.indexOf('<a ') !== -1) {
            var openCount = (value.match(/<a\s/gi) || []).length;
            var closeCount = (value.match(/<\/a>/gi) || []).length;
            if (openCount !== closeCount) {
                warnings.push('Anchor HTML may be unbalanced.');
            }
        }

        if (originalLength > 40 && value.length > 0) {
            var ratio = value.length / originalLength;
            if (ratio > 1.8) {
                warnings.push('Text is much longer than the original and may overflow.');
            }
        }

        if (preset && value && preset.toLowerCase() === value.toLowerCase()) {
            warnings.push('Value equals preset (no override needed).');
        }

        var $feedback = $('.ytct-inline-feedback[data-key="' + key + '"]');
        $feedback.removeClass('ytct-inline-error ytct-inline-warning').empty();
        $input.removeClass('ytct-field-error ytct-field-warning');

        if (issues.length) {
            $feedback.addClass('ytct-inline-error').text(issues[0]);
            $input.addClass('ytct-field-error');
        } else if (warnings.length) {
            $feedback.addClass('ytct-inline-warning').text(warnings[0]);
            $input.addClass('ytct-field-warning');
        }
    }

    function refreshFieldMetrics() {
        $form.find('input[name^="strings["], textarea[name^="strings["]').each(function() {
            var $input = $(this);
            updateFieldMetrics($input.data('key'), $input);
        });
    }

    function updateFieldMetrics(key, $input) {
        if (!key || !$input || !$input.length) {
            return;
        }

        var value = $input.val() || '';
        var preset = $input.attr('data-preset') || '';
        var originalLength = parseInt($input.attr('data-original-length'), 10) || 0;
        var parts = [value.length + ' chars'];

        if (originalLength > 0) {
            parts.push('orig ' + originalLength);
        }

        if (preset.length > 0 && value !== preset) {
            parts.push('customized');
        }

        $('.ytct-field-metrics[data-key="' + key + '"]').text(parts.join(' | '));
    }

    function resetFieldToPreset(key) {
        var $input = $form.find('[name="strings[' + key + ']"]');
        if (!$input.length) {
            return;
        }

        var preset = $input.attr('data-preset') || '';
        $input.val(preset);
        validateField(key, $input);
        updatePreviewForKey(key, preset);
        updateFieldMetrics(key, $input);
    }

    function saveSettings() {
        var $btn = $saveBtn;
        var originalText = $btn.html();

        $btn.prop('disabled', true).html('<span class="ytct-spinner"></span> ' + ytctAdmin.strings.saving);

        var formData = new FormData($form[0]);
        formData.append('action', 'ytct_save_settings');
        formData.append('nonce', ytctAdmin.nonce);
        formData.set('settings_locale', getScopeLocale());

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || ytctAdmin.strings.saved, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function resetSettings() {
        var $btn = $resetBtn;
        var originalText = $btn.html();

        $btn.prop('disabled', true).html(ytctAdmin.strings.resetting);

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_reset_settings',
                nonce: ytctAdmin.nonce,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || ytctAdmin.strings.resetSuccess, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function exportSettings() {
        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_export_settings',
                nonce: ytctAdmin.nonce,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data) {
                    var dataStr = JSON.stringify(response.data.data, null, 2);
                    var blob = new Blob([dataStr], { type: 'application/json' });
                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = response.data.filename || 'yt-consent-translations-export.json';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            }
        });
    }

    function showImportModal() {
        $modal.addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function hideImportModal() {
        $modal.removeClass('show');
        $('body').css('overflow', '');
        $('#ytct-import-file').val('');
        $('.ytct-file-name').hide();
    }

    function importSettings() {
        var fileInput = $('#ytct-import-file')[0];

        if (!fileInput.files || !fileInput.files[0]) {
            showMessage(ytctAdmin.strings.invalidFile, 'error');
            return;
        }

        var file = fileInput.files[0];
        if (!file.name.toLowerCase().endsWith('.json')) {
            showMessage(ytctAdmin.strings.invalidFile, 'error');
            return;
        }

        var $btn = $('#ytct-import-submit');
        var originalText = $btn.html();

        $btn.prop('disabled', true).html(ytctAdmin.strings.importing);

        var formData = new FormData();
        formData.append('action', 'ytct_import_settings');
        formData.append('nonce', ytctAdmin.nonce);
        formData.append('settings_locale', getScopeLocale());
        formData.append('import_file', file);

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    hideImportModal();
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || ytctAdmin.strings.importSuccess, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function loadLanguagePreset(language) {
        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_load_language',
                nonce: ytctAdmin.nonce,
                language: language,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data && response.data.translations) {
                    $.each(response.data.translations, function(key, value) {
                        var $input = $form.find('[name="strings[' + key + ']"]');
                        if ($input.length) {
                            $input.val(value);
                            $input.attr('data-preset', value);
                        }
                    });

                    refreshAllUiState();
                    markDirtyIfNeeded();
                    showMessage(ytctAdmin.strings.languageLoaded, 'success');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            }
        });
    }

    function loadScope(scopeLocale) {
        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_load_scope',
                nonce: ytctAdmin.nonce,
                settings_locale: scopeLocale
            },
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, true);
                    showMessage(ytctAdmin.strings.scopeLoaded, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            }
        });
    }

    function applyScopePayload(scope, setScopeSelect) {
        if (!scope) {
            return;
        }

        if (scope.scopeLocale) {
            $scopeHidden.val(scope.scopeLocale);
            if (setScopeSelect !== false) {
                $scopeSelect.val(scope.scopeLocale);
            }
        }

        if (scope.options) {
            if (scope.options.enabled !== undefined) {
                $('#ytct-enabled').prop('checked', !!scope.options.enabled);
            }

            if (scope.options.language) {
                $languageSelect.val(scope.options.language);
            }
        }

        if (scope.presetTranslations) {
            $.each(scope.presetTranslations, function(key, value) {
                var $input = $form.find('[name="strings[' + key + ']"]');
                if ($input.length) {
                    $input.attr('data-preset', value);
                }
            });
        }

        if (scope.effectiveStrings) {
            $.each(scope.effectiveStrings, function(key, value) {
                var $input = $form.find('[name="strings[' + key + ']"]');
                if ($input.length) {
                    $input.val(value);
                }
            });
        }

        if (scope.snapshots) {
            updateSnapshotSelect(scope.snapshots);
        }

        if (scope.health) {
            renderHealth(scope.health);
        }

        if (scope.quality) {
            renderQuality(scope.quality);
        }

        refreshAllUiState();
    }

    function updateSnapshotSelect(snapshots) {
        var $select = $('#ytct-snapshot-select');
        $select.empty();
        $select.append($('<option>', {
            value: '',
            text: ytctAdmin.strings.selectSnapshot
        }));

        if (!Array.isArray(snapshots)) {
            return;
        }

        snapshots.forEach(function(snapshot) {
            if (!snapshot || !snapshot.id) {
                return;
            }

            var label = (snapshot.label || 'snapshot') + ' - ' + (snapshot.created_at || '');
            $select.append($('<option>', {
                value: snapshot.id,
                text: label
            }));
        });
    }

    function restoreSnapshot() {
        var snapshotId = $('#ytct-snapshot-select').val();
        if (!snapshotId) {
            showMessage(ytctAdmin.strings.selectSnapshotFirst, 'error');
            return;
        }

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_restore_snapshot',
                nonce: ytctAdmin.nonce,
                settings_locale: getScopeLocale(),
                snapshot_id: snapshotId
            },
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || ytctAdmin.strings.restored, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            }
        });
    }

    function runQualityCheck() {
        var originalText = $qualityBtn.html();
        $qualityBtn.prop('disabled', true).text(ytctAdmin.strings.qualityCheckRunning);

        var formData = new FormData($form[0]);
        formData.append('action', 'ytct_quality_check');
        formData.append('nonce', ytctAdmin.nonce);
        formData.set('settings_locale', getScopeLocale());

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.data && response.data.quality) {
                    renderQuality(response.data.quality);
                    if (response.data.quality.status === 'ok') {
                        showMessage(ytctAdmin.strings.qualityCheckOk, 'success');
                    } else {
                        showMessage(ytctAdmin.strings.qualityCheckFailed, 'error');
                    }
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            },
            complete: function() {
                $qualityBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function renderQuality(quality) {
        if (!quality) {
            $qualityReport.removeClass('show').empty();
            return;
        }

        var html = [];
        if (Array.isArray(quality.issues) && quality.issues.length) {
            html.push('<strong>Issues</strong><ul>');
            quality.issues.forEach(function(issue) {
                html.push('<li>' + escapeHtml(issue) + '</li>');
            });
            html.push('</ul>');
        }

        if (Array.isArray(quality.warnings) && quality.warnings.length) {
            html.push('<strong>Warnings</strong><ul>');
            quality.warnings.forEach(function(warning) {
                html.push('<li>' + escapeHtml(warning) + '</li>');
            });
            html.push('</ul>');
        }

        if (!html.length) {
            html.push('<p>No quality issues found.</p>');
        }

        $qualityReport
            .removeClass('ytct-quality-ok ytct-quality-warning ytct-quality-error')
            .addClass('show ytct-quality-' + (quality.status || 'ok'))
            .html(html.join(''));
    }

    function runHealthCheck() {
        var originalText = $healthBtn.html();
        $healthBtn.prop('disabled', true).text(ytctAdmin.strings.healthCheckRunning);

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_health_check',
                nonce: ytctAdmin.nonce,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data && response.data.health) {
                    renderHealth(response.data.health);
                    showMessage(ytctAdmin.strings.healthCheckOk, 'success');
                } else {
                    showMessage((response.data && response.data.message) || ytctAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(ytctAdmin.strings.error, 'error');
            },
            complete: function() {
                $healthBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function renderHealth(health) {
        var $panel = $('#ytct-health-panel');
        var $list = $('#ytct-health-list');
        if (!$panel.length || !$list.length || !health) {
            return;
        }

        $panel
            .removeClass('ytct-health-healthy ytct-health-notice ytct-health-warning')
            .addClass('ytct-health-' + (health.status || 'healthy'));

        var items = [];
        if (Array.isArray(health.issues) && health.issues.length) {
            health.issues.forEach(function(issue) {
                items.push('<li class="ytct-health-issue">' + escapeHtml(issue) + '</li>');
            });
        }

        if (Array.isArray(health.warnings) && health.warnings.length) {
            health.warnings.forEach(function(warning) {
                items.push('<li class="ytct-health-warning">' + escapeHtml(warning) + '</li>');
            });
        }

        if (!items.length) {
            items.push('<li class="ytct-health-ok">No compatibility issues reported.</li>');
        }

        $list.html(items.join(''));
    }

    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function showMessage(text, type) {
        $message
            .removeClass('ytct-message-success ytct-message-error')
            .addClass('ytct-message-' + type)
            .text(text)
            .addClass('show');

        setTimeout(function() {
            $message.removeClass('show');
        }, 5000);

        $('html, body').animate({
            scrollTop: $message.offset().top - 50
        }, 300);
    }

    $(document).ready(init);

})(jQuery);
