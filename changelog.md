# Changelog

All notable changes to YT Consent Translations will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.1] - 2026-01-21

### Fixed
- **Critical**: Admin settings page not appearing in WordPress admin menu
- The `admin_init` hook was running after `admin_menu` hook, preventing the settings menu from being registered
- Changed admin initialization to run directly instead of through delayed hook

---

## [1.2.0] - 2026-01-21

### Changed
- **Major Refactoring**: Moved all translations from hardcoded PHP to external JSON files
- Each language now has its own `languages/{code}.json` file (36 files)
- Implemented lazy loading - only requested language is loaded into memory
- Per-language caching instead of loading all translations at once

### Performance
- Reduced memory usage by ~95% on typical requests
- Translations are loaded on-demand instead of all at once
- Added `clear_cache()` method for memory management

### Code Quality
- Separation of concerns: data (JSON) separated from logic (PHP)
- Easier to maintain and update individual language files
- Better code organization following best practices

---

## [1.1.0] - 2026-01-21

### Added
- 30 new language presets (36 total languages)
  - Chinese (zh), Spanish (es), French (fr), Portuguese (pt)
  - Russian (ru), Japanese (ja), Indonesian (id), Italian (it)
  - Dutch (nl), Polish (pl), Vietnamese (vi), Thai (th)
  - Ukrainian (uk), Czech (cs), Greek (el), Romanian (ro)
  - Hungarian (hu), Swedish (sv), Danish (da), Finnish (fi)
  - Norwegian (nb), Hebrew (he), Malay (ms), Bengali (bn)
  - Persian (fa), Tamil (ta), Telugu (te), Marathi (mr)
  - Swahili (sw), Filipino (tl)
- Extended WordPress locale mapping for auto-detection

### Changed
- Updated README with new language table
- Total translations: 756 (36 languages × 21 strings)

### Security
- Replaced `wp_kses_post()` with strict sanitization (only `<a>` tags allowed)
- Added server-side file type validation for imports (.json only)
- Added file size limit (100KB) for import uploads
- Changed export from GET to POST method (prevents nonce exposure)
- Added tab ID validation in JavaScript
- Improved multisite cleanup using `get_sites()` instead of raw SQL
- Fixed version mismatch between plugin header and constant

---

## [1.0.0] - 2026-01-21

### Added
- Initial release
- Support for 21 YOOtheme Pro 5 Consent Manager strings
- 6 pre-configured language presets:
  - English (en) - Default
  - Turkish (tr)
  - Hindi (hi)
  - Korean (ko)
  - Arabic (ar)
  - German (de)
- Auto language detection from WordPress locale
- Tabbed admin interface (Banner, Modal, Categories, Buttons)
- Enable/Disable toggle
- Import/Export settings as JSON
- AJAX-powered save functionality
- Responsive admin design
- RTL support for Arabic
- WordPress multisite compatibility
- Clean uninstall (removes all plugin data)

### Translatable Strings
1. Banner Text
2. Banner Privacy Link
3. Accept Button
4. Reject Button
5. Manage Settings Button
6. Modal Title
7. Modal Content
8. Modal Privacy Link
9. Functional Title
10. Functional Description
11. Preferences Title
12. Preferences Description
13. Statistics Title
14. Statistics Description
15. Marketing Title
16. Marketing Description
17. Show Services
18. Hide Services
19. Accept All Button
20. Reject All Button
21. Save Button

### Technical
- Uses WordPress `gettext` filter
- Targets `yootheme` text domain
- Implements caching for performance
- Follows WordPress coding standards
- Minimum PHP 7.4
- Minimum WordPress 5.0
