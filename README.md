# YT Consent Translations

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/ercanatay/yt-consent-translations)
[![WordPress Tested](https://img.shields.io/badge/WordPress-5.0%2B-green.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

**Easily translate YOOtheme Pro 5 Consent Manager texts from the WordPress admin panel.**

A lightweight WordPress plugin that allows you to customize all 21 text strings in the YOOtheme Pro 5 Consent Manager directly from your admin panel. No coding required!

## 🎯 Features

- ✅ **21 Translatable Strings** - All consent manager texts
- ✅ **6 Pre-configured Languages** - Ready to use out of the box
- ✅ **Auto Language Detection** - Automatically uses WordPress default language
- ✅ **Easy Admin Interface** - Tabbed UI for better organization
- ✅ **Import/Export** - Backup and restore your translations as JSON
- ✅ **No Coding Required** - Simple point-and-click interface
- ✅ **WPML/Polylang Compatible** - Works with multilingual plugins

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

### Method 3: Composer (Coming Soon)

```bash
composer require ercanatay/yt-consent-translations
```

## ⚙️ Configuration

1. Go to **Settings → YT Consent Translations**
2. Select your language preset or set to "Auto"
3. Customize any text as needed
4. Click **Save Changes**

### Quick Start

1. **Auto Mode**: Select "Auto (WordPress Default)" to automatically use translations matching your WordPress language
2. **Manual Mode**: Select a specific language and customize the texts
3. **Custom**: Modify any preset text to match your brand voice

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

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Adding New Languages

To add a new language, edit `includes/class-strings.php`:

```php
// Add to $languages array
'fr' => 'Français',

// Add to $locale_map array
'fr_FR' => 'fr',

// Add translations in get_all_translations()
'fr' => [
    'banner_text' => 'Nous utilisons des cookies...',
    // ... other strings
],
```

## 📝 Changelog

### 1.0.0 (2026-01-21)
- Initial release
- 6 language presets (EN, TR, HI, KO, AR, DE)
- Auto language detection
- Import/Export functionality
- Tabbed admin interface

See [CHANGELOG.md](CHANGELOG.md) for full history.

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
