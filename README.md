# Cybokron Consent Manager Translations for YOOtheme Pro

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.5.1-blue.svg)](https://github.com/ercanatay/cybokron-consent-manager-translations-yootheme)
[![WordPress Tested](https://img.shields.io/badge/WordPress-5.0--7.0-green.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

**Easily translate YOOtheme Pro 5 Consent Manager texts from the WordPress admin panel.**

A lightweight WordPress plugin that allows you to customize all 21 text strings in the YOOtheme Pro 5 Consent Manager directly from your admin panel. No coding required!

## 🎯 Features

- ✅ **21 Translatable Strings** - All consent manager texts
- ✅ **36 Pre-configured Languages** - Ready to use out of the box
- ✅ **Auto Language Detection** - Automatically uses WordPress default language
- ✅ **Locale Scope Support** - Store different overrides per WordPress locale
- ✅ **Easy Admin Interface** - Tabbed UI for better organization
- ✅ **Live Preview & Inline QA** - Real-time preview with field-level validation
- ✅ **Compatibility Health Check** - Detect potential YOOtheme string drift
- ✅ **Snapshots & Rollback** - Restore previous settings in one click
- ✅ **Import/Export** - Backup and restore your translations as JSON
- ✅ **WordPress.org Update Status** - Site-wide periodic check visibility in plugin settings
- ✅ **No Coding Required** - Simple point-and-click interface
- ✅ **WPML/Polylang Compatible** - Works with multilingual plugins

## 📸 Screenshots

### Admin Settings - Banner Tab
![Banner Tab](assets/screenshots/01-banner-tab.jpg)

### Admin Settings - Modal Tab
![Modal Tab](assets/screenshots/02-modal-tab.jpg)

### Admin Settings - Categories Tab
![Categories Tab](assets/screenshots/03-categories-tab.jpg)

### Admin Settings - Buttons Tab
![Buttons Tab](assets/screenshots/04-buttons-tab.jpg)

### Frontend - Consent Popup
![Consent Popup](assets/screenshots/05-consent-popup.jpg)

## 🌐 Supported Languages (36 Languages)

| Language | Code | Language | Code |
|----------|------|----------|------|
| English | `en` | Chinese | `zh` |
| Spanish | `es` | French | `fr` |
| Portuguese | `pt` | Russian | `ru` |
| Japanese | `ja` | Indonesian | `id` |
| Italian | `it` | Dutch | `nl` |
| Polish | `pl` | Vietnamese | `vi` |
| Thai | `th` | Ukrainian | `uk` |
| Czech | `cs` | Greek | `el` |
| Romanian | `ro` | Hungarian | `hu` |
| Swedish | `sv` | Danish | `da` |
| Finnish | `fi` | Norwegian | `nb` |
| Hebrew | `he` | Malay | `ms` |
| Bengali | `bn` | Persian | `fa` |
| Tamil | `ta` | Telugu | `te` |
| Marathi | `mr` | Swahili | `sw` |
| Filipino | `tl` | Turkish | `tr` |
| Hindi | `hi` | Korean | `ko` |
| Arabic | `ar` | German | `de` |

> **Auto Detection:** Set language to "Auto" and the plugin will automatically detect your WordPress site language!

## 📋 Translatable Strings

### Banner
- Banner text
- Privacy Policy link
- Accept button
- Reject button
- Manage Settings button

### Modal
- Modal title
- Modal content
- Privacy Policy link

### Categories
- Functional (title & description)
- Preferences (title & description)
- Statistics (title & description)
- Marketing (title & description)

### Buttons
- Show Services
- Hide Services
- Accept All
- Reject All
- Save

## 📦 Installation

### Method 1: Upload via WordPress Admin

1. Download the latest release ZIP file
2. Go to **Plugins → Add New → Upload Plugin**
3. Choose the downloaded ZIP file and click **Install Now**
4. Activate the plugin

### Method 2: Manual Installation

1. Download and extract the plugin
2. Upload the `cybokron-consent-manager-translations-yootheme` folder to `/wp-content/plugins/`
3. Activate through **Plugins** menu in WordPress

### Method 3: Composer (GitHub VCS)

`ercanatay/cybokron-consent-manager-translations-yootheme` is not currently distributed on Packagist.
To install with Composer, add this repository as a VCS source first:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ercanatay/cybokron-consent-manager-translations-yootheme"
        }
    ],
    "require": {
        "ercanatay/cybokron-consent-manager-translations-yootheme": "^1.3"
    }
}
```

```bash
composer update ercanatay/cybokron-consent-manager-translations-yootheme
```

## ⚙️ Configuration

1. Go to **Settings → Cybokron Consent Manager Translations for YOOtheme Pro**
2. Select your language preset or set to "Auto"
3. Configure **WordPress.org Update Status** checks (site-wide toggle)
4. Customize any text as needed
5. Click **Save Changes**

### Quick Start

1. **Auto Mode**: Select "Auto (WordPress Default)" to automatically use translations matching your WordPress language
2. **Manual Mode**: Select a specific language and customize the texts
3. **Custom**: Modify any preset text to match your brand voice

### WordPress.org Update Status Panel

- Source: WordPress core plugin update metadata (`update_plugins` transient)
- Scope: site-wide setting (not locale-scoped)
- Default: enabled on new installs
- Check interval: every 12 hours (`twicedaily`)
- Manual trigger: **Check Now** button in plugin settings
- Status labels are human-readable in admin (for example: `Up to date`, `Update available`)

## 🔧 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- YOOtheme Pro 5 theme with Consent Manager enabled

## 📚 Documentation

### How It Works

This plugin uses WordPress's `gettext` filter to intercept and replace YOOtheme Pro's consent manager strings. It specifically targets the `yootheme` text domain.

```php
// Example: How strings are filtered
add_filter('gettext', function($translated, $original, $domain) {
    if ($domain === 'yootheme') {
        // Return custom translation
    }
    return $translated;
}, 20, 3);
```

### Filter Hooks

```php
// Modify translations programmatically
add_filter('cybocoma_translations', function($translations, $language) {
    $translations['button_accept'] = 'I Agree';
    return $translations;
}, 10, 2);
```

### Constants

```php
// Disable the plugin programmatically
define('CYBOCOMA_DISABLED', true);
```

### Locale-aware Storage

Starting with `1.3.0`, settings are stored per locale scope (for example: `en_US`, `tr_TR`).
This is useful for WPML/Polylang scenarios where each locale needs different consent wording.

### Quality & Compatibility Tooling

- Built-in quality checks for placeholders, link integrity, and text length warnings
- Compatibility health panel to surface potential YOOtheme source string changes
- Snapshot history with rollback support

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Adding New Languages

1. Edit `includes/class-strings.php` to register the language:

```php
// Add to $languages array
'fr' => 'Français',

// Add to $locale_map array
'fr_FR' => 'fr',
```

2. Create a JSON file in the `languages/` directory (e.g., `languages/fr.json`) with all 21 string keys:

```json
{
    "banner_text": "Nous utilisons des cookies...",
    "banner_link": "Lisez notre <a href=\"%s\">Politique de confidentialité</a>.",
    "button_accept": "Accepter",
    "button_reject": "Refuser",
    "button_settings": "Gérer les paramètres",
    "modal_title": "Paramètres de confidentialité",
    "modal_content": "...",
    "modal_content_link": "En savoir plus dans notre <a href=\"%s\">Politique de confidentialité</a>.",
    "functional_title": "Fonctionnel",
    "preferences_title": "Préférences",
    "statistics_title": "Statistiques",
    "marketing_title": "Marketing",
    "functional_content": "...",
    "preferences_content": "...",
    "statistics_content": "...",
    "marketing_content": "...",
    "show_services": "Afficher les services",
    "hide_services": "Masquer les services",
    "modal_accept": "Tout accepter",
    "modal_reject": "Tout refuser",
    "modal_save": "Enregistrer"
}
```

## 📝 Changelog

### 1.5.1 (2026-08-10)
- **Compatibility**: Verified against WordPress 7.0.3 on PHP 8.4.19 with YOOtheme Pro 5.0.37 active and `WP_DEBUG` enabled — clean activation, settings screen and front end render, all AJAX endpoints (save, health check, quality check, export, snapshots) return success, and zero PHP warnings, notices, deprecations or fatal errors were produced
- **Tested**: up to WordPress 7.0 (raised from 6.9); PHP support range extended to 8.4
- **Housekeeping**: `LICENSE` now carries the full GPL-2.0 text, so automated license detection reports GPL-2.0 correctly (previously only the copyright notice and a link were present)
- **Note**: No functional changes to the plugin

### 1.5.0 (2026-03-31)
- **New**: Added 4 missing service consent text strings — OpenStreetMap, Google Maps, Vimeo, and YouTube service descriptions are now translatable (fixes [#34](https://github.com/ercanatay/cybokron-consent-manager-translations-yootheme/issues/34))
- **New**: New "Services" tab group in admin settings exposes these strings for all 36 language presets
- **Updated**: Total translatable strings increased from 21 to 25; total translations 756 → 900 (36 languages × 25 strings)

### 1.4.4 (2026-03-07)
- **Fixed**: Language fallback in sanitize_options now correctly defaults to `auto` instead of `en`, consistent with the default introduced in 1.4.3

### 1.4.3 (2026-02-27)
- **Fixed**: WPML/Polylang compatibility — translations now auto-detect from WordPress locale when no locale-specific settings exist, fixing missing translations on multilingual sites
- **Changed**: Default language setting changed from `en` to `auto` for better out-of-the-box multilingual support
- **Packaging**: Removed `.distignore` and `.github` from distribution to resolve Plugin Check errors
- **Tested**: up to WordPress 6.9

### 1.4.2 (2026-02-17)
- **Changed**: Admin sidebar and settings page header now use white plugin icon for better visibility on dark admin backgrounds

### 1.4.1 (2026-02-17)
- **CI**: Fixed ABSPATH guard validation to accept `WP_UNINSTALL_PLUGIN` as valid guard for `uninstall.php`

### 1.4.0 (2026-02-17)
- **New**: Plugin icon displayed in WordPress admin sidebar menu
- **New**: Plugin icon added to settings page header
- **Changed**: Plugin menu moved to top-level admin menu with custom icon for better visibility
- **Release Sync**: Updated plugin/readme/composer metadata to `1.4.0`

### 1.3.19 (2026-02-17)
- **CI**: Added GitHub Actions workflow for automatic WordPress.org SVN deployment on release publish
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.19`

### 1.3.18 (2026-02-17)
- **Contributors**: Removed invalid WordPress.org username `ercanatay` from Contributors field, kept only valid `cybokron` account
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.18`

### 1.3.17 (2026-02-16)
- **Plugin Check**: Removed discouraged `load_plugin_textdomain()` call (WordPress 4.6+ loads translations automatically for WordPress.org hosted plugins)
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.17`

### 1.3.16 (2026-02-16)
- **Security**: Added `wp_kses` output sanitization to gettext filter for defense-in-depth against stored XSS in custom translation strings
- **Security**: Added type check after `maybe_unserialize` in locale option scan to prevent object injection
- **Performance**: Added rate-limiting (1-hour interval) to health report DB persistence to avoid writes on every frontend page load
- **Improvement**: Replaced `uniqid()` with `wp_generate_uuid4()` for snapshot IDs (cryptographically stronger, WordPress-native)
- **Improvement**: Replaced `file_get_contents` with `wp_json_file_decode` (WP 5.9+) for language JSON loading with legacy fallback
- **Improvement**: Implemented `load_plugin_textdomain()` for proper i18n text domain loading
- **Cleanup**: Removed unnecessary `flush_rewrite_rules()` calls from activation/deactivation hooks
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.16`

### 1.3.15 (2026-02-16)
- **Text Domain Fix**: Corrected all gettext text domain values from `cybokron-consent-manager-translations-yootheme-main` to `cybokron-consent-manager-translations-yootheme` to match the plugin slug exactly
- **Packaging**: Removed `tests/` and `scripts/` directories from the distribution package entirely
- **Distribution**: Added `.distignore` to prevent development-only files from being included in release archives
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.15`

### 1.3.14 (2026-02-14)
- **Plugin Check Compatibility**: Aligned plugin header and gettext text-domain usage to `cybokron-consent-manager-translations-yootheme` matching the plugin slug
- **Packaging Cleanup**: Removed root hidden/workflow entries (`.distignore`, `.github`) that triggered Plugin Check warnings
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.14`

### 1.3.13 (2026-02-14)
- **WordPress.org Review Compliance**: Aligned text domain usage with slug `cybokron-consent-manager-translations-yootheme` in all gettext calls
- **Unique Prefix Refactor**: Replaced generic identifiers with `cybocoma_` / `CYBOCOMA_` across classes, constants, hooks, options, and AJAX endpoints
- **Packaging**: Added `.distignore` release rules to exclude non-production paths (`tests/`, `scripts/`, and development metadata) from distribution archives
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.13`

### 1.3.12 (2026-02-12)
- **i18n Fix**: Restored the expected text domain `cybokron-consent-manager-translations-yootheme` across plugin/admin/health/strings/updater modules
- **Plugin Check Compatibility**: Updated plugin header `Text Domain` to `cybokron-consent-manager-translations-yootheme`
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.12`
- **Release**: Published package/tag `v1.3.12` on GitHub Releases

### 1.3.11 (2026-02-12)
- **Naming Compliance**: Renamed plugin display name and slug to `Cybokron Consent Manager Translations for YOOtheme Pro` / `cybokron-consent-manager-translations-yootheme`
- **Metadata Alignment**: Updated plugin header metadata, admin settings page slug, text domain, and package file naming for the new slug
- **QA**: Re-ran test suite, PHP syntax checks, and static scan with clean results
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.11`
- **Release**: Published package/tag `v1.3.11` on GitHub Releases

### 1.3.10 (2026-02-11)
- **Bugfix (i18n)**: Localized admin statistics summary text generated in JavaScript (`{customized}/{total} customized ({percent}%)`) via `wp_localize_script`
- **PR Review**: Reviewed merged PR scope for `#28`, `#29`, and `#30` before release cut
- **QA**: Re-ran test suite, PHP syntax checks, JSON validation, and static scan with clean results
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.10`
- **Release**: Published package/tag `v1.3.10` on GitHub Releases

### 1.3.9 (2026-02-10)
- **Security**: Sanitized admin live-preview link HTML to allow only safe anchor output (text + `<a href title>`) with enforced `rel="noopener noreferrer"`
- **SQL Safety**: Removed redundant `esc_sql()` wrappers around `$wpdb->esc_like()` wildcard lookups in scoped option scans and uninstall cleanup queries
- **Performance**: Persisted internal snapshot/health/updater options with `autoload=false` to reduce unnecessary front-end option autoload pressure
- **Uninstall Hygiene**: Added updater cron unscheduling (`cybocoma_updater_cron_check`) for both single-site and multisite uninstall flows, including pre-6.1 fallback handling
- **Cache Consistency**: Reset translator `original_to_key` map when clearing runtime caches and aligned test bootstrap `update_option()` signature with core usage
- **Release Sync**: Updated plugin/readme/composer metadata to `1.3.9`

### 1.3.8 (2026-02-10)
- **Accessibility**: Added semantic ARIA tab patterns to settings tabs (`tablist`, `tab`, `tabpanel`) and synchronized visibility state (`hidden` + `aria-hidden`)
- **Keyboard Navigation**: Added Left/Right and Home/End support for tab switching with focus management
- **UX Consistency**: Standardized active tab roving `tabindex` handling (`0` for active, `-1` for inactive)
- **Packaging**: Removed accidental `.Jules` development artifact from release contents

### 1.3.7 (2026-02-10)
- **WordPress.org Compliance**: Removed custom updater hooks that modify WordPress update routines (`site_transient_update_plugins`, `pre_set_site_transient_update_plugins`, `auto_update_plugin`)
- **Updater UX**: Reworked updater flow to read WordPress.org update metadata and display localized status labels (fixes raw `up_to_date` output)
- **i18n**: Standardized plugin text domain usage to `cybokron-consent-manager-translations-yootheme` across plugin/admin/health/string modules
- **Compatibility**: Removed deprecated `wp_targeted_link_rel()` usage from admin sanitization path
- **Plugin Check**: Added direct file access guards to CLI test files and aligned test coverage with the new updater behavior

### 1.3.6 (2026-02-09)
- **Security**: Added reverse tabnabbing protection for sanitized `target="_blank"` consent links using `wp_targeted_link_rel()`
- **Security**: Added admin AJAX response hardening with `X-Content-Type-Options: nosniff` and conditional `X-Frame-Options` fallback behavior
- **Tests**: Added `test_admin_headers.php` regression coverage with header helper stubs in the CLI bootstrap
- **Packaging**: Removed non-production `.jules` release artifact
- **Release Sync**: Updated plugin/readme/composer metadata and text-domain version references to `1.3.6`

### 1.3.5 (2026-02-09)
- **Language Completeness**: Completed remaining Romanian preset gap by localizing `button_accept` from `Accept` to `Acceptă`
- **Release Sync**: Updated plugin/readme/composer metadata and text-domain version references to `1.3.5`

### 1.3.4 (2026-02-09)
- **Updater**: Added GitHub stable auto-update channel powered by WordPress Upgrader (`releases/latest` with `zipball_url` fallback)
- **Admin UX**: Added site-wide updater panel with enable toggle, status fields, and a manual **Check Now** action
- **Automation**: Added 12-hour update check scheduling (`twicedaily`) with silent retry and persisted last-error reporting
- **Tests**: Added updater test coverage (`test_updater.php`) and expanded bootstrap stubs for updater-related WordPress functions
- **Cleanup**: Added updater option cleanup on uninstall and synchronized release metadata to `1.3.4`

### 1.3.3 (2026-02-08)
- **Double-Check Release**: Re-ran full syntax/tests/JSON/security validation sweep across the plugin with clean results
- **Release Sync**: Updated version/text-domain metadata for the current release package

### 1.3.2 (2026-02-08)
- **Plugin Check**: Hardened admin request parsing with sanitized `filter_input()` paths and explicit nonce-context handling for upload payload access
- **i18n**: Updated all translatable calls to the current text domain and fixed ordered placeholder guidance for `%s`/`%1$s` translator-facing help text
- **Packaging**: Removed non-production root artifacts flagged by Plugin Check (`.github`, `.gitignore`, shell runner, and daily markdown report)
- **Options Scan**: Added object-cache layer for locale option aggregation and documented unavoidable wildcard queries in scoped uninstall/summary paths

### 1.3.1 (2026-02-08)
- **i18n Fix**: Replaced hardcoded snapshot/quality-check admin messages in JavaScript with localized strings from PHP
- **Uninstall Fix**: Corrected multisite uninstall block structure so scoped option cleanup always runs in the intended loop
- **Test Cleanup**: Removed redundant `gmdate()` shim from test bootstrap to avoid dead-code shadowing
- **Docs**: Clarified the new-language contribution flow with explicit JSON file requirements

### 1.3.0 (2026-02-08)
- **Per-Locale Overrides**: Added locale-scoped settings storage to support different translation overrides by WordPress locale
- **Compatibility Monitoring**: Added runtime health reporting to detect potential YOOtheme consent source string drift
- **Developer API**: Implemented documented `cybocoma_translations` filter and `CYBOCOMA_DISABLED` runtime constant behavior
- **Admin UX**: Added live preview, inline validation feedback, unsaved-change guard, field reset actions, and quality check tooling
- **Recovery**: Added snapshot history and one-click rollback for settings
- **Release Gate**: Added lightweight PHP tests and CI workflow (`release-gate.yml`) with syntax/JSON/security checks

### 1.2.7 (2026-02-08)
- **Performance**: Replaced repeated `in_array()` scans with constant-time lookup maps (`isset`) in import/language/placeholder validation paths
- **Validation**: Added and adopted `CYBOCOMA_Strings::is_valid_language()` to centralize language whitelist checks
- **Code Quality**: Cleaned up internal iteration logic in translation loading and normalized admin/settings indentation consistency
- **Tooling**: Added security policy, daily review GitHub Action, and maintenance scripts (`scan_code.py`, `daily_report.py`, `fix_indentation.py`)
- **Docs**: Updated contributing workflow and compatibility/readme details

### 1.2.6 (2026-02-06)
- **Auto Language**: Fixed admin preset preview for `auto` mode to use the detected WordPress language instead of defaulting to English
- **Behavior**: `custom_strings` now stores only values different from the selected preset (diff-based save/import normalization)
- **Validation**: Added blocking backend validation for required `%s`/`%1$s` placeholders in privacy policy link fields
- **AJAX**: Updated language preset loading flow to resolve `auto` to the effective language without changing the selected value

### 1.2.5 (2026-01-22)
- **Code Style**: Converted 4-space indentation to tabs in all PHP files
- **WordPress Coding Standards**: Full compliance with WPCS indentation rules
- No functional changes, only whitespace formatting

### 1.2.4 (2026-01-22)
- **Bugfix**: Fixed missing strict comparison in `has_placeholder()` method (`class-strings.php:390`)

### 1.2.3 (2026-01-22)
- **Security**: Enforced POST method for settings export (removed GET support)
- **Tools**: Added JSON validation script (`scripts/validate_json.py`)
- **Code Quality**: Added strict comparison to all `in_array()` calls

### 1.2.2 (2026-01-21)
- **Code Quality**: Full WordPress Plugin Check compliance
- Added `cybocoma_` prefix to all template variables
- Improved input sanitization with `wp_unslash()`
- Enhanced `$_FILES` validation with proper `isset` checks
- Reduced readme tags from 6 to 5

### 1.2.1 (2026-01-21)
- **Bugfix**: Fixed admin settings page not appearing
- The `admin_init` hook was running after `admin_menu`, preventing menu registration

### 1.2.0 (2026-01-21)
- **Major Refactoring**: Translations moved to external JSON files
- Implemented lazy loading - only requested language loaded into memory
- Reduced memory usage by ~95% on typical requests
- Better code organization (separation of data and logic)

### 1.1.0 (2026-01-21)
- Added 30 new language presets (36 total)
- Chinese, Spanish, French, Portuguese, Russian, Japanese
- Indonesian, Italian, Dutch, Polish, Vietnamese, Thai
- Ukrainian, Czech, Greek, Romanian, Hungarian, Swedish
- Danish, Finnish, Norwegian, Hebrew, Malay, Bengali
- Persian, Tamil, Telugu, Marathi, Swahili, Filipino
- Extended WordPress locale mapping for auto-detection

### 1.0.0 (2026-01-21)
- Initial release
- 6 language presets (EN, TR, HI, KO, AR, DE)
- Auto language detection
- Import/Export functionality
- Tabbed admin interface

## ⚠️ Important Notes

### String Matching Dependency

This plugin works by matching the **exact original English strings** from YOOtheme Pro's Consent Manager. If YOOtheme updates their theme and changes any of these strings (even a single character), the translation for that string will not work until the plugin is updated.

**What to do if translations stop working after a YOOtheme update:**
1. Check if YOOtheme changed any consent manager strings
2. [Report an issue](https://github.com/ercanatay/cybokron-consent-manager-translations-yootheme/issues) with the new string
3. Wait for a plugin update or manually edit the `includes/class-strings.php` file

### Tested With
- YOOtheme Pro 5.x
- WordPress 5.0 - 7.0
- PHP 7.4 - 8.4

## 🐛 Known Issues

- None at this time

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 👤 Author

**Ercan ATAY**

- Website: [ercanatay.com](https://www.ercanatay.com/en/)
- GitHub: [@ercanatay](https://github.com/ercanatay)
- LinkedIn: [in/ercanatay](https://linkedin.com/in/ercanatay)
- Twitter: [@ercanataytr](https://twitter.com/ercanataytr)

## 🙏 Acknowledgments

- [YOOtheme](https://yootheme.com) for the amazing YOOtheme Pro theme
- [WordPress](https://wordpress.org) for the platform
- The open-source community

## ⭐ Support

If you find this plugin helpful, please consider:

- Giving it a ⭐ on GitHub
- Sharing it with others who might benefit
- [Reporting issues](https://github.com/ercanatay/cybokron-consent-manager-translations-yootheme/issues) you encounter

---

Made with ❤️ in Istanbul, Turkey
