/**
 * YT Consent Translations - Admin Script
 *
 * @package YT_Consent_Translations
 */

(function($) {
    'use strict';

    // Cache DOM elements
    var $form = $('#ytct-settings-form');
    var $saveBtn = $('#ytct-save-btn');
    var $resetBtn = $('#ytct-reset-btn');
    var $exportBtn = $('#ytct-export-btn');
    var $importBtn = $('#ytct-import-btn');
    var $languageSelect = $('#ytct-language');
    var $message = $('#ytct-message');
    var $tabs = $('.ytct-tab');
    var $tabContents = $('.ytct-tab-content');
    var $modal = $('#ytct-import-modal');

    /**
     * Initialize
     */
    function init() {
        bindEvents();
        initTabs();
    }

    /**
     * Bind events
     */
    function bindEvents() {
        // Save settings
        $form.on('submit', function(e) {
            e.preventDefault();
            saveSettings();
        });

        // Reset settings
        $resetBtn.on('click', function(e) {
            e.preventDefault();
            if (confirm(ytctAdmin.strings.confirmReset)) {
                resetSettings();
            }
        });

        // Export settings
        $exportBtn.on('click', function(e) {
            e.preventDefault();
            exportSettings();
        });

        // Import button - show modal
        $importBtn.on('click', function(e) {
            e.preventDefault();
            showImportModal();
        });

        // Language change - load preset
        $languageSelect.on('change', function() {
            loadLanguagePreset($(this).val());
        });

        // Tab switching
        $tabs.on('click', function() {
            switchTab($(this).data('tab'));
        });

        // Modal close
        $('.ytct-modal-close, .ytct-modal-overlay').on('click', function(e) {
            if (e.target === this) {
                hideImportModal();
            }
        });

        // Import form submit
        $('#ytct-import-form').on('submit', function(e) {
            e.preventDefault();
            importSettings();
        });

        // File input change
        $('#ytct-import-file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).siblings('.ytct-file-name').text(fileName).show();
            }
        });
    }

    /**
     * Initialize tabs
     */
    function initTabs() {
        // Show first tab by default
        var activeTab = $tabs.filter('.active').data('tab') || 'banner';
        switchTab(activeTab);
    }

    /**
     * Switch tab
     */
    function switchTab(tabId) {
        $tabs.removeClass('active');
        $tabs.filter('[data-tab="' + tabId + '"]').addClass('active');

        $tabContents.removeClass('active');
        $('#ytct-tab-' + tabId).addClass('active');
    }

    /**
     * Save settings via AJAX
     */
    function saveSettings() {
        var $btn = $saveBtn;
        var originalText = $btn.html();

        // Disable button and show loading
        $btn.prop('disabled', true).html('<span class="ytct-spinner"></span> ' + ytctAdmin.strings.saving);

        // Collect form data
        var formData = new FormData($form[0]);
        formData.append('action', 'ytct_save_settings');
        formData.append('nonce', ytctAdmin.nonce);

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message || ytctAdmin.strings.saved, 'success');
                } else {
                    showMessage(response.data.message || ytctAdmin.strings.error, 'error');
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

    /**
     * Reset settings via AJAX
     */
    function resetSettings() {
        var $btn = $resetBtn;
        var originalText = $btn.html();

        $btn.prop('disabled', true).html(ytctAdmin.strings.resetting);

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_reset_settings',
                nonce: ytctAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message || ytctAdmin.strings.resetSuccess, 'success');
                    
                    // Reset form fields
                    $languageSelect.val('en');
                    $form.find('input[type="text"], textarea').val('');
                    $form.find('input[name="enabled"]').prop('checked', true);
                    
                    // Load English preset
                    loadLanguagePreset('en');
                } else {
                    showMessage(response.data.message || ytctAdmin.strings.error, 'error');
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

    /**
     * Export settings
     */
    function exportSettings() {
        window.location.href = ytctAdmin.ajaxUrl + '?action=ytct_export_settings&nonce=' + ytctAdmin.nonce;
    }

    /**
     * Show import modal
     */
    function showImportModal() {
        $modal.addClass('show');
        $('body').css('overflow', 'hidden');
    }

    /**
     * Hide import modal
     */
    function hideImportModal() {
        $modal.removeClass('show');
        $('body').css('overflow', '');
        $('#ytct-import-file').val('');
        $('.ytct-file-name').hide();
    }

    /**
     * Import settings via AJAX
     */
    function importSettings() {
        var fileInput = $('#ytct-import-file')[0];
        
        if (!fileInput.files || !fileInput.files[0]) {
            showMessage(ytctAdmin.strings.invalidFile, 'error');
            return;
        }

        var file = fileInput.files[0];
        if (!file.name.endsWith('.json')) {
            showMessage(ytctAdmin.strings.invalidFile, 'error');
            return;
        }

        var $btn = $('#ytct-import-submit');
        var originalText = $btn.html();

        $btn.prop('disabled', true).html(ytctAdmin.strings.importing);

        var formData = new FormData();
        formData.append('action', 'ytct_import_settings');
        formData.append('nonce', ytctAdmin.nonce);
        formData.append('import_file', file);

        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    hideImportModal();
                    showMessage(response.data.message || ytctAdmin.strings.importSuccess, 'success');
                    
                    // Update form with imported values
                    if (response.data.options) {
                        updateFormWithOptions(response.data.options);
                    }
                } else {
                    showMessage(response.data.message || ytctAdmin.strings.error, 'error');
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

    /**
     * Load language preset via AJAX
     */
    function loadLanguagePreset(language) {
        $.ajax({
            url: ytctAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'ytct_load_language',
                nonce: ytctAdmin.nonce,
                language: language
            },
            success: function(response) {
                if (response.success && response.data.translations) {
                    // Update form fields with translations
                    $.each(response.data.translations, function(key, value) {
                        var $input = $form.find('[name="strings[' + key + ']"]');
                        if ($input.length) {
                            $input.val(value);
                        }
                    });
                    
                    showMessage(ytctAdmin.strings.languageLoaded, 'success');
                }
            }
        });
    }

    /**
     * Update form with options
     */
    function updateFormWithOptions(options) {
        if (options.enabled !== undefined) {
            $form.find('input[name="enabled"]').prop('checked', options.enabled);
        }

        if (options.language) {
            $languageSelect.val(options.language);
        }

        if (options.custom_strings) {
            // First load the language preset
            loadLanguagePreset(options.language || 'en');
            
            // Then override with custom strings
            setTimeout(function() {
                $.each(options.custom_strings, function(key, value) {
                    var $input = $form.find('[name="strings[' + key + ']"]');
                    if ($input.length && value) {
                        $input.val(value);
                    }
                });
            }, 500);
        }
    }

    /**
     * Show message
     */
    function showMessage(text, type) {
        $message
            .removeClass('ytct-message-success ytct-message-error')
            .addClass('ytct-message-' + type)
            .text(text)
            .addClass('show');

        // Auto hide after 5 seconds
        setTimeout(function() {
            $message.removeClass('show');
        }, 5000);

        // Scroll to message
        $('html, body').animate({
            scrollTop: $message.offset().top - 50
        }, 300);
    }

    // Initialize when document is ready
    $(document).ready(init);

})(jQuery);
