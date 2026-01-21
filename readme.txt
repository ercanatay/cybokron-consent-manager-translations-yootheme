=== YT Consent Translations ===
Contributors: ercanatay
Tags: yootheme, consent-manager, gdpr, cookie-consent, translation
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.2.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily translate YOOtheme Pro 5 Consent Manager texts from the WordPress admin panel. No coding required!

== Description ==

YT Consent Translations allows you to customize all text strings in the YOOtheme Pro 5 Consent Manager directly from your WordPress admin panel.

**Features:**

* Translate all 21 Consent Manager strings
* 36 pre-configured language presets
* Easy-to-use tabbed interface
* Import/Export settings as JSON
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

The plugin stores translations per WordPress installation. For true multilingual support with WPML or Polylang, you may need to configure the plugin separately for each language or use the multilingual plugin's string translation feature.

= How do I backup my translations? =

Use the Export button to download a JSON file of your current settings. You can Import this file later to restore your translations.

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
