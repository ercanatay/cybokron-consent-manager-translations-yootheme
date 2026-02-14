/**
 * Cybokron Consent Manager Translations for YOOtheme Pro - Admin Script
 *
 * @package CYBOCOMA_Consent_Translations
 */

(function($) {
    'use strict';

    var $form = $('#cybocoma-settings-form');
    var $saveBtn = $('#cybocoma-save-btn');
    var $resetBtn = $('#cybocoma-reset-btn');
    var $exportBtn = $('#cybocoma-export-btn');
    var $importBtn = $('#cybocoma-import-btn');
    var $qualityBtn = $('#cybocoma-quality-btn');
    var $healthBtn = $('#cybocoma-health-btn');
    var $restoreBtn = $('#cybocoma-restore-btn');
    var $checkUpdateBtn = $('#cybocoma-check-update-btn');
    var $languageSelect = $('#cybocoma-language');
    var $scopeSelect = $('#cybocoma-scope-locale');
    var $scopeHidden = $('#cybocoma-settings-locale');
    var $message = $('#cybocoma-message');
    var $tabs = $('.cybocoma-tab');
    var $tabContents = $('.cybocoma-tab-content');
    var $modal = $('#cybocoma-import-modal');
    var $copyLocaleModal = $('#cybocoma-copy-locale-modal');
    var $qualityReport = $('#cybocoma-quality-report');
    var $searchInput = $('#cybocoma-search-strings');
    var $searchClear = $('#cybocoma-search-clear');
    var $noResults = $('#cybocoma-no-results');
    var $copyLocaleBtn = $('#cybocoma-copy-locale-btn');

    var state = {
        initialHash: '',
        isDirty: false,
        isSearching: false
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
            if (window.confirm(cybocomaAdmin.strings.confirmReset)) {
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

        $checkUpdateBtn.on('click', function(e) {
            e.preventDefault();
            checkUpdateNow();
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

        $tabs.on('keydown', function(e) {
            if (e.altKey || e.ctrlKey || e.metaKey) {
                return;
            }

            var key = e.key || e.which || e.keyCode;
            var index = $tabs.index(this);
            var nextIndex = index;

            if (key === 'ArrowRight' || key === 39) {
                nextIndex = index + 1;
            } else if (key === 'ArrowLeft' || key === 37) {
                nextIndex = index - 1;
            } else if (key === 'Home' || key === 36) {
                nextIndex = 0;
            } else if (key === 'End' || key === 35) {
                nextIndex = $tabs.length - 1;
            } else {
                return;
            }

            e.preventDefault();

            if (nextIndex >= $tabs.length) {
                nextIndex = 0;
            } else if (nextIndex < 0) {
                nextIndex = $tabs.length - 1;
            }

            var $nextTab = $tabs.eq(nextIndex);
            if (!$nextTab.length) {
                return;
            }

            switchTab($nextTab.data('tab'));
            $nextTab.focus();
        });

        $('.cybocoma-modal-close, .cybocoma-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                hideImportModal();
                hideCopyLocaleModal();
            }
        });

        $('#cybocoma-import-form').on('submit', function(e) {
            e.preventDefault();
            importSettings();
        });

        $('#cybocoma-import-file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).siblings('.cybocoma-file-name').text(fileName).show();
            }
        });

        $searchInput.on('input', function() {
            filterStrings($(this).val());
        });

        $searchClear.on('click', function() {
            $searchInput.val('');
            filterStrings('');
            $searchInput.focus();
        });

        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.keyCode === 83)) {
                e.preventDefault();
                if (!$saveBtn.prop('disabled')) {
                    saveSettings();
                }
            }
        });

        $copyLocaleBtn.on('click', function(e) {
            e.preventDefault();
            showCopyLocaleModal();
        });

        $('#cybocoma-copy-locale-form').on('submit', function(e) {
            e.preventDefault();
            copyLocale();
        });

        $form.on('input', 'input[name^="strings["], textarea[name^="strings["]', function() {
            var $input = $(this);
            var key = $input.data('key');
            validateField(key, $input);
            updatePreviewForKey(key, $input.val());
            updateFieldMetrics(key, $input);
            updateStatisticsBar();
            markDirtyIfNeeded();
        });

        $form.on('click', '.cybocoma-reset-field', function(e) {
            e.preventDefault();
            var key = $(this).data('key');
            resetFieldToPreset(key);
            markDirtyIfNeeded();
        });

        $form.on('change', '#cybocoma-enabled, #cybocoma-update-channel-enabled', function() {
            markDirtyIfNeeded();
        });

        $(window).on('beforeunload', function(e) {
            if (!state.isDirty) {
                return;
            }

            e.preventDefault();
            e.returnValue = cybocomaAdmin.strings.unsavedChanges;
            return cybocomaAdmin.strings.unsavedChanges;
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

        $tabs.removeClass('active')
            .attr('aria-selected', 'false')
            .attr('tabindex', '-1');

        $tabs.filter('[data-tab="' + tabId + '"]')
            .addClass('active')
            .attr('aria-selected', 'true')
            .attr('tabindex', '0');

        $tabContents.removeClass('active')
            .prop('hidden', true)
            .attr('aria-hidden', 'true');

        $('#cybocoma-tab-' + tabId)
            .addClass('active')
            .prop('hidden', false)
            .attr('aria-hidden', 'false');
    }

    function getScopeLocale() {
        return $scopeHidden.val() || $scopeSelect.val() || '';
    }

    function serializeFormState() {
        var payload = {
            enabled: $('#cybocoma-enabled').is(':checked'),
            update_channel_enabled: $('#cybocoma-update-channel-enabled').is(':checked'),
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
        updateStatisticsBar();
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
            $targets.html(sanitizeAnchorHtml(htmlValue));
            return;
        }

        $targets.text(value || '');
    }

    function sanitizeAnchorHtml(html) {
        var $container = $('<div>');
        var $temp = $('<div>').html(html);

        $temp.contents().each(function() {
            if (this.nodeType === 3) {
                $container.append(document.createTextNode(this.nodeValue));
            } else if (this.nodeType === 1 && this.nodeName === 'A') {
                var $a = $('<a>');
                var href = $(this).attr('href');
                if (href) {
                    $a.attr('href', href);
                }
                var title = $(this).attr('title');
                if (title) {
                    $a.attr('title', title);
                }
                $a.attr('rel', 'noopener noreferrer');
                $a.text($(this).text());
                $container.append($a);
            }
        });

        return $container.html();
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

        var $feedback = $('.cybocoma-inline-feedback[data-key="' + key + '"]');
        $feedback.removeClass('cybocoma-inline-error cybocoma-inline-warning').empty();
        $input.removeClass('cybocoma-field-error cybocoma-field-warning');

        if (issues.length) {
            $feedback.addClass('cybocoma-inline-error').text(issues[0]);
            $input.addClass('cybocoma-field-error');
        } else if (warnings.length) {
            $feedback.addClass('cybocoma-inline-warning').text(warnings[0]);
            $input.addClass('cybocoma-field-warning');
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

        $('.cybocoma-field-metrics[data-key="' + key + '"]').text(parts.join(' | '));
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

        $btn.prop('disabled', true).html('<span class="cybocoma-spinner"></span> ' + cybocomaAdmin.strings.saving);

        var formData = new FormData($form[0]);
        formData.append('action', 'cybocoma_save_settings');
        formData.append('nonce', cybocomaAdmin.nonce);
        formData.set('settings_locale', getScopeLocale());

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || cybocomaAdmin.strings.saved, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function resetSettings() {
        var $btn = $resetBtn;
        var originalText = $btn.html();

        $btn.prop('disabled', true).html(cybocomaAdmin.strings.resetting);

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_reset_settings',
                nonce: cybocomaAdmin.nonce,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || cybocomaAdmin.strings.resetSuccess, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function exportSettings() {
        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_export_settings',
                nonce: cybocomaAdmin.nonce,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data) {
                    var dataStr = JSON.stringify(response.data.data, null, 2);
                    var blob = new Blob([dataStr], { type: 'application/json' });
                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = response.data.filename || 'cybokron-consent-manager-translations-yootheme-export.json';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
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
        $('#cybocoma-import-file').val('');
        $('.cybocoma-file-name').hide();
    }

    function importSettings() {
        var fileInput = $('#cybocoma-import-file')[0];

        if (!fileInput.files || !fileInput.files[0]) {
            showMessage(cybocomaAdmin.strings.invalidFile, 'error');
            return;
        }

        var file = fileInput.files[0];
        if (!file.name.toLowerCase().endsWith('.json')) {
            showMessage(cybocomaAdmin.strings.invalidFile, 'error');
            return;
        }

        var $btn = $('#cybocoma-import-submit');
        var originalText = $btn.html();

        $btn.prop('disabled', true).html(cybocomaAdmin.strings.importing);

        var formData = new FormData();
        formData.append('action', 'cybocoma_import_settings');
        formData.append('nonce', cybocomaAdmin.nonce);
        formData.append('settings_locale', getScopeLocale());
        formData.append('import_file', file);

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    hideImportModal();
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || cybocomaAdmin.strings.importSuccess, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function loadLanguagePreset(language) {
        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_load_language',
                nonce: cybocomaAdmin.nonce,
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
                    showMessage(cybocomaAdmin.strings.languageLoaded, 'success');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            }
        });
    }

    function loadScope(scopeLocale) {
        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_load_scope',
                nonce: cybocomaAdmin.nonce,
                settings_locale: scopeLocale
            },
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, true);
                    showMessage(cybocomaAdmin.strings.scopeLoaded, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
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
                $('#cybocoma-enabled').prop('checked', !!scope.options.enabled);
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

        if (scope.updater) {
            renderUpdater(scope.updater);
        }

        refreshAllUiState();
    }

    function renderUpdater(updater) {
        if (!updater) {
            return;
        }

        $('#cybocoma-update-channel-enabled').prop('checked', !!updater.enabled);
        $('#cybocoma-updater-current-version').text(updater.currentVersion || '');
        $('#cybocoma-updater-latest-version').text(updater.latestVersion || 'Unknown');
        $('#cybocoma-updater-last-check').text(formatIsoDate(updater.lastCheckedAt));
        $('#cybocoma-updater-status').text(formatUpdaterStatus(updater.status, !!updater.updateAvailable, updater.statusLabel));
        $('#cybocoma-updater-last-install').text(formatIsoDate(updater.lastInstallAt));
        $('#cybocoma-updater-last-error').text(updater.lastError || 'None');
    }

    function formatUpdaterStatus(status, updateAvailable, statusLabel) {
        if (statusLabel) {
            return statusLabel;
        }

        var map = {
            idle: 'Idle',
            up_to_date: 'Up to date',
            update_available: 'Update available',
            error: 'Error',
            installing: 'Installing',
            updated: 'Updated',
            update_failed: 'Update failed'
        };

        var normalized = status && map[status] ? status : 'idle';
        if (updateAvailable && normalized !== 'updated') {
            return map.update_available;
        }

        return map[normalized];
    }

    function formatIsoDate(value) {
        if (!value) {
            return 'Never';
        }

        var date = new Date(value);
        if (isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString();
    }

    function updateSnapshotSelect(snapshots) {
        var $select = $('#cybocoma-snapshot-select');
        $select.empty();
        $select.append($('<option>', {
            value: '',
            text: cybocomaAdmin.strings.selectSnapshot
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
        var snapshotId = $('#cybocoma-snapshot-select').val();
        if (!snapshotId) {
            showMessage(cybocomaAdmin.strings.selectSnapshotFirst, 'error');
            return;
        }

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_restore_snapshot',
                nonce: cybocomaAdmin.nonce,
                settings_locale: getScopeLocale(),
                snapshot_id: snapshotId
            },
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message || cybocomaAdmin.strings.restored, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            }
        });
    }

    function checkUpdateNow() {
        var originalText = $checkUpdateBtn.html();
        $checkUpdateBtn.prop('disabled', true).text(cybocomaAdmin.strings.checkUpdateRunning);

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_check_update_now',
                nonce: cybocomaAdmin.nonce,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data && response.data.updater) {
                    var type = 'success';
                    var updater = response.data.updater;
                    renderUpdater(updater);

                    if (updater.status === 'error' || updater.status === 'update_failed') {
                        type = 'error';
                    }

                    showMessage(response.data.message || cybocomaAdmin.strings.checkUpdateNoChange, type);
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            },
            complete: function() {
                $checkUpdateBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function runQualityCheck() {
        var originalText = $qualityBtn.html();
        $qualityBtn.prop('disabled', true).text(cybocomaAdmin.strings.qualityCheckRunning);

        var formData = new FormData($form[0]);
        formData.append('action', 'cybocoma_quality_check');
        formData.append('nonce', cybocomaAdmin.nonce);
        formData.set('settings_locale', getScopeLocale());

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success && response.data && response.data.quality) {
                    renderQuality(response.data.quality);
                    if (response.data.quality.status === 'ok') {
                        showMessage(cybocomaAdmin.strings.qualityCheckOk, 'success');
                    } else {
                        showMessage(cybocomaAdmin.strings.qualityCheckFailed, 'error');
                    }
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
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
            .removeClass('cybocoma-quality-ok cybocoma-quality-warning cybocoma-quality-error')
            .addClass('show cybocoma-quality-' + (quality.status || 'ok'))
            .html(html.join(''));
    }

    function runHealthCheck() {
        var originalText = $healthBtn.html();
        $healthBtn.prop('disabled', true).text(cybocomaAdmin.strings.healthCheckRunning);

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_health_check',
                nonce: cybocomaAdmin.nonce,
                settings_locale: getScopeLocale()
            },
            success: function(response) {
                if (response.success && response.data && response.data.health) {
                    renderHealth(response.data.health);
                    showMessage(cybocomaAdmin.strings.healthCheckOk, 'success');
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            },
            complete: function() {
                $healthBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function renderHealth(health) {
        var $panel = $('#cybocoma-health-panel');
        var $list = $('#cybocoma-health-list');
        if (!$panel.length || !$list.length || !health) {
            return;
        }

        $panel
            .removeClass('cybocoma-health-healthy cybocoma-health-notice cybocoma-health-warning')
            .addClass('cybocoma-health-' + (health.status || 'healthy'));

        var items = [];
        if (Array.isArray(health.issues) && health.issues.length) {
            health.issues.forEach(function(issue) {
                items.push('<li class="cybocoma-health-issue">' + escapeHtml(issue) + '</li>');
            });
        }

        if (Array.isArray(health.warnings) && health.warnings.length) {
            health.warnings.forEach(function(warning) {
                items.push('<li class="cybocoma-health-warning">' + escapeHtml(warning) + '</li>');
            });
        }

        if (!items.length) {
            items.push('<li class="cybocoma-health-ok">No compatibility issues reported.</li>');
        }

        $list.html(items.join(''));
    }

    function filterStrings(query) {
        query = (query || '').toLowerCase().trim();

        if (!query) {
            state.isSearching = false;
            $searchClear.hide();
            $noResults.hide();
            $tabContents.find('.cybocoma-string-group').show();
            var activeTab = $tabs.filter('.active').data('tab') || 'banner';
            switchTab(activeTab);
            return;
        }

        state.isSearching = true;
        $searchClear.show();

        $tabContents
            .addClass('active')
            .prop('hidden', false)
            .attr('aria-hidden', 'false');

        var visibleCount = 0;
        $tabContents.find('.cybocoma-string-group').each(function() {
            var $group = $(this);
            var label = ($group.find('.cybocoma-string-label').text() || '').toLowerCase();
            var original = ($group.find('.cybocoma-original').text() || '').toLowerCase();
            var value = ($group.find('.cybocoma-input').val() || '').toLowerCase();

            if (label.indexOf(query) !== -1 || original.indexOf(query) !== -1 || value.indexOf(query) !== -1) {
                $group.show();
                visibleCount++;
            } else {
                $group.hide();
            }
        });

        if (visibleCount === 0) {
            $noResults.show();
        } else {
            $noResults.hide();
        }
    }

    function showCopyLocaleModal() {
        $copyLocaleModal.addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function hideCopyLocaleModal() {
        $copyLocaleModal.removeClass('show');
        $('body').css('overflow', '');
        $('#cybocoma-copy-source-locale').val('');
    }

    function copyLocale() {
        var sourceLocale = $('#cybocoma-copy-source-locale').val();
        if (!sourceLocale) {
            showMessage(cybocomaAdmin.strings.selectSourceLocale, 'error');
            return;
        }

        if (!window.confirm(cybocomaAdmin.strings.confirmCopyLocale)) {
            return;
        }

        var $btn = $('#cybocoma-copy-locale-submit');
        var originalText = $btn.html();

        $btn.prop('disabled', true).html('<span class="cybocoma-spinner"></span> ' + cybocomaAdmin.strings.copyLocaleRunning);

        $.ajax({
            url: cybocomaAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cybocoma_copy_locale',
                nonce: cybocomaAdmin.nonce,
                settings_locale: getScopeLocale(),
                source_locale: sourceLocale
            },
            success: function(response) {
                if (response.success && response.data && response.data.scope) {
                    hideCopyLocaleModal();
                    applyScopePayload(response.data.scope, false);
                    showMessage(response.data.message, 'success');
                    captureInitialState();
                } else {
                    showMessage((response.data && response.data.message) || cybocomaAdmin.strings.error, 'error');
                }
            },
            error: function() {
                showMessage(cybocomaAdmin.strings.error, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    function updateStatisticsBar() {
        var total = 0;
        var customized = 0;

        $form.find('input[name^="strings["], textarea[name^="strings["]').each(function() {
            total++;
            var value = $(this).val() || '';
            var preset = $(this).attr('data-preset') || '';
            if (value !== '' && value !== preset) {
                customized++;
            }
        });

        var percent = total > 0 ? Math.round((customized / total) * 100) : 0;
        var summaryTemplate = (cybocomaAdmin.strings && cybocomaAdmin.strings.statsSummary) ? cybocomaAdmin.strings.statsSummary : '{customized}/{total} customized ({percent}%)';
        var summaryText = summaryTemplate
            .replace('{customized}', String(customized))
            .replace('{total}', String(total))
            .replace('{percent}', String(percent));

        $('#cybocoma-stats-bar-fill').css('width', percent + '%');
        $('#cybocoma-stats-text').text(summaryText);
    }

    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function showMessage(text, type) {
        $message
            .removeClass('cybocoma-message-success cybocoma-message-error')
            .addClass('cybocoma-message-' + type)
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
