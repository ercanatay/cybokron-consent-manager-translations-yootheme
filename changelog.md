# Changelog

All notable changes to YT Consent Translations will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
