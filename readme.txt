=== YT Consent Translations ===
Contributors: ercanatay
Tags: yootheme, consent-manager, gdpr, cookie-consent, translation
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.3.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily translate YOOtheme Pro 5 Consent Manager texts from the WordPress admin panel. No coding required!

== Description ==

YT Consent Translations allows you to customize all text strings in the YOOtheme Pro 5 Consent Manager directly from your WordPress admin panel.

**Features:**

* Translate all 21 Consent Manager strings
* 36 pre-configured language presets
* Locale-scoped overrides for multilingual setups
* Easy-to-use tabbed interface
* Live preview, inline QA checks, and compatibility health panel
* Snapshot history with rollback support
* Import/Export settings as JSON
* GitHub stable auto-update channel (site-wide toggle, 12-hour checks)
* No coding required
* Compatible with WPML and Polylang

**Supported Strings:**

* Banner text and buttons (Accept, Reject, Manage Settings)
* Privacy Settings modal content
* Category titles (Functional, Preferences, Statistics, Marketing)
* Category descriptions
* Modal buttons (Accept all, Reject all, Save)
* Show/Hide Services toggles

**Pre-configured Languages (36):**

English, Chinese, Spanish, French, Portuguese, Russian, Japanese, Indonesian, Italian, Dutch, Polish, Vietnamese, Thai, Ukrainian, Czech, Greek, Romanian, Hungarian, Swedish, Danish, Finnish, Norwegian, Hebrew, Malay, Bengali, Persian, Tamil, Telugu, Marathi, Swahili, Filipino, Turkish, Hindi, Korean, Arabic, German

== Installation ==

1. Upload the `yt-consent-translations` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings → YT Consent Translations to configure

== Frequently Asked Questions ==

= Does this plugin require YOOtheme Pro? =

Yes, this plugin is designed specifically for translating the YOOtheme Pro 5 Consent Manager. It won't have any effect without YOOtheme Pro installed.

= Can I add my own custom language? =

Yes! Simply select any language preset and modify the texts to your needs. Your custom translations will override the preset values.

= Does it work with multilingual plugins? =

The plugin supports locale-scoped settings (for example `en_US`, `tr_TR`) so you can maintain different overrides per locale while still using shared language presets.

= How do I backup my translations? =

Use the Export button to download a JSON file of your current settings. You can Import this file later to restore your translations.

= How does GitHub auto-update work? =

Enable the "GitHub Stable Auto Update" toggle from plugin settings. The plugin checks the latest stable release every 12 hours and auto-installs newer versions using WordPress Upgrader. If GitHub is temporarily unreachable, it retries automatically and shows the last error in the updater panel.

= My translations are not showing up =

1. Make sure the plugin is enabled (check the toggle in settings)
2. Clear any caching plugins
3. Clear your browser cache
4. Make sure you're using YOOtheme Pro 5 with the Consent Manager enabled

== Screenshots ==

1. Main settings page with language selector
2. Banner strings translation
3. Modal content translation
4. Category translations
5. Import/Export functionality

== Changelog ==

= 1.3.5 =
* Completed Romanian preset translation gap: updated `button_accept` from `Accept` to `Acceptă`.
* Synced plugin metadata/versioning and text-domain references to `1.3.5`.

= 1.3.4 =
* Added GitHub stable auto-update channel using WordPress Upgrader with `releases/latest` + `zipball_url` fallback.
* Added site-wide updater controls/status panel and manual "Check Now" action in settings.
* Added 12-hour scheduled update checks (`twicedaily`) with retry-on-failure and persisted last-error reporting.
* Added updater-focused test coverage and uninstall cleanup for updater options.
* Synced plugin metadata/versioning to `1.3.4`.

= 1.3.3 =
* Full double-check release: reran syntax/tests/JSON/security scans across the plugin and validated clean results.
* Maintenance: version/text-domain sync for current release packaging.

= 1.3.2 =
* Plugin Check hardening: added sanitized `filter_input()` request handling in admin flows and clarified nonce handling for upload endpoints.
* i18n: aligned all translatable strings with updated text domain and fixed ordered placeholder usage with translator guidance.
* Packaging: removed non-production root artifacts flagged by Plugin Check (`.github`, `.gitignore`, shell runner, and daily markdown report).
* Performance/quality: added cache layer for locale option scan and documented one-time uninstall wildcard query usage.

= 1.3.1 =
* Fixed i18n coverage for snapshot/quality-check admin messages by replacing hardcoded JavaScript text with localized strings
* Fixed uninstall script block structure for multisite cleanup flow
* Removed redundant `gmdate()` test bootstrap shim to avoid dead-code shadowing in PHP environments
* Docs: Clarified "add new language" workflow with explicit JSON-file requirements

= 1.3.0 =
* Added locale-scoped settings storage for multilingual workflows (WPML/Polylang friendly)
* Added compatibility health reporting for potential YOOtheme source string drift
* Implemented documented extension APIs: `ytct_translations` filter and `YTCT_DISABLED` constant behavior
* Added live preview panel, inline field validation, field-level reset actions, and unsaved-change protection
* Added settings snapshots with one-click rollback
* Added release-gate workflow and lightweight PHP tests

= 1.2.7 =
* Performance: Replaced repeated array scans with constant-time lookup maps (`isset`) in language/placeholder validation paths
* Improved: Added `YTCT_Strings::is_valid_language()` helper and reused it in admin validation flow
* Quality: Minor internal cleanups and formatting consistency updates for admin/settings rendering
* Tooling: Added security policy, daily review workflow, and maintenance scripts for code scanning/reporting
* Docs: Updated contributor workflow and compatibility notes in repository documentation

= 1.2.6 =
* Fixed: Auto language preset now resolves to detected WordPress locale in admin preview/load flows
* Improved: custom_strings are now stored as diff-only values against selected preset (save/import normalization)
* Validation: Save and import now block invalid privacy link strings missing required %s or %1$s placeholders

= 1.2.5 =
* Code Style: Converted 4-space indentation to tabs in all PHP files
* WordPress Coding Standards compliance
* No functional changes, only whitespace formatting

= 1.2.4 =
* Bugfix: Fixed missing strict comparison in has_placeholder() method (class-strings.php:390)

= 1.2.3 =
* Security: Enforced POST method for settings export (removed GET support)
* Added JSON validation script for language files (scripts/validate_json.py)
* Code quality: Added strict comparison to all in_array() calls

= 1.2.2 =
* Fixed: WordPress Plugin Check compliance - all warnings resolved
* Added ytct_ prefix to all template variables
* Improved input sanitization with wp_unslash()
* Enhanced $_FILES validation

= 1.2.1 =
* Fixed: Admin settings page not appearing due to hook timing issue
* The admin_init hook was running after admin_menu, preventing menu registration

= 1.2.0 =
* Major refactoring: moved translations to external JSON files
* Each language now has its own JSON file (36 files)
* Implemented lazy loading - only requested language is loaded
* Reduced memory usage by ~95% on typical requests
* Better code organization and maintainability

= 1.1.0 =
* Added 30 new language presets (36 total)
* Chinese, Spanish, French, Portuguese, Russian, Japanese
* Indonesian, Italian, Dutch, Polish, Vietnamese, Thai
* Ukrainian, Czech, Greek, Romanian, Hungarian, Swedish
* Danish, Finnish, Norwegian, Hebrew, Malay, Bengali
* Persian, Tamil, Telugu, Marathi, Swahili, Filipino
* Extended WordPress locale mapping for auto-detection
* Total translations: 756 (36 languages × 21 strings)

= 1.0.0 =
* Initial release
* 6 language presets included
* 21 translatable strings
* Import/Export functionality
* Tabbed admin interface

== Upgrade Notice ==

= 1.3.5 =
Language completeness maintenance release for Romanian preset correction and release metadata sync.

= 1.3.4 =
Feature release with GitHub stable auto-update channel, scheduled background checks, manual check action, and updater observability in admin settings.

= 1.3.3 =
Maintenance verification release with full project re-check and synchronized version metadata.

= 1.3.2 =
Plugin Check compliance release focused on admin request hardening, i18n placeholder/domain fixes, and production package cleanup.

= 1.3.1 =
Maintenance bugfix release with localized admin UI messages, multisite uninstall cleanup fix, and test bootstrap cleanup.

= 1.3.0 =
Feature release with locale-scoped overrides, live preview, compatibility monitoring, rollback snapshots, and release gate tests.

= 1.2.7 =
Maintenance release with performance optimizations, validation hardening, and documentation/tooling updates.

= 1.2.6 =
Behavioral bugfix release for auto mode preview, diff-based custom string storage, and placeholder safety validation.

= 1.2.5 =
Code style improvement - tabs instead of spaces for WordPress Coding Standards compliance.

= 1.2.4 =
Bugfix release - fixed missing strict comparison in has_placeholder() method.

= 1.2.3 =
Security improvement: Export now uses POST method only.

= 1.2.2 =
Code quality improvements for WordPress.org compliance.

= 1.2.1 =
Bugfix release - fixes admin settings page not appearing issue.

= 1.2.0 =
Major performance improvement with lazy loading translations from JSON files.

= 1.1.0 =
Added 30 new language presets for a total of 36 languages.

= 1.0.0 =
Initial release of YT Consent Translations.
