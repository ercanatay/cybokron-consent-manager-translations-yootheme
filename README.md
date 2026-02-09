# YT Consent Translations

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.3.5-blue.svg)](https://github.com/ercanatay/yt-consent-translations)
[![WordPress Tested](https://img.shields.io/badge/WordPress-5.0--6.9-green.svg)](https://wordpress.org)
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
- ✅ **GitHub Stable Auto Update** - Optional site-wide channel with background checks and auto-install
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
2. Upload the `yt-consent-translations` folder to `/wp-content/plugins/`
3. Activate through **Plugins** menu in WordPress

### Method 3: Composer (GitHub VCS)

`ercanatay/yt-consent-translations` is not currently distributed on Packagist.
To install with Composer, add this repository as a VCS source first:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ercanatay/yt-consent-translations"
        }
    ],
    "require": {
        "ercanatay/yt-consent-translations": "^1.3"
    }
}
```

```bash
composer update ercanatay/yt-consent-translations
```

## ⚙️ Configuration

1. Go to **Settings → YT Consent Translations**
2. Select your language preset or set to "Auto"
3. Configure **GitHub Stable Auto Update** channel (site-wide toggle)
4. Customize any text as needed
5. Click **Save Changes**

### Quick Start

1. **Auto Mode**: Select "Auto (WordPress Default)" to automatically use translations matching your WordPress language
2. **Manual Mode**: Select a specific language and customize the texts
3. **Custom**: Modify any preset text to match your brand voice

### GitHub Stable Auto Update Channel

- Source: GitHub latest stable release (`releases/latest`)
- Package strategy: release asset if available, otherwise GitHub `zipball_url`
- Scope: site-wide setting (not locale-scoped)
- Default: enabled on new installs
- Check interval: every 12 hours (`twicedaily`)
- Failure behavior: automatic retry on next cycle + last error surfaced in admin updater panel
- Manual trigger: **Check Now** button in plugin settings

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
add_filter('ytct_translations', function($translations, $language) {
    $translations['button_accept'] = 'I Agree';
    return $translations;
}, 10, 2);
```

### Constants

```php
// Disable the plugin programmatically
define('YTCT_DISABLED', true);
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
- **Developer API**: Implemented documented `ytct_translations` filter and `YTCT_DISABLED` runtime constant behavior
- **Admin UX**: Added live preview, inline validation feedback, unsaved-change guard, field reset actions, and quality check tooling
- **Recovery**: Added snapshot history and one-click rollback for settings
- **Release Gate**: Added lightweight PHP tests and CI workflow (`release-gate.yml`) with syntax/JSON/security checks

### 1.2.7 (2026-02-08)
- **Performance**: Replaced repeated `in_array()` scans with constant-time lookup maps (`isset`) in import/language/placeholder validation paths
- **Validation**: Added and adopted `YTCT_Strings::is_valid_language()` to centralize language whitelist checks
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
- Added `ytct_` prefix to all template variables
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
2. [Report an issue](https://github.com/ercanatay/yt-consent-translations/issues) with the new string
3. Wait for a plugin update or manually edit the `includes/class-strings.php` file

### Tested With
- YOOtheme Pro 5.x
- WordPress 5.0 - 6.9
- PHP 7.4 - 8.3

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
- [Reporting issues](https://github.com/ercanatay/yt-consent-translations/issues) you encounter

---

Made with ❤️ in Istanbul, Turkey
