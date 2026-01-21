## What's New in v1.2.0

### 🚀 Major Performance Improvements
- **Translations moved to external JSON files** - Each language now has its own file
- **Lazy loading implemented** - Only the requested language is loaded into memory
- **~95% memory reduction** on typical requests

### 📁 New File Structure
```
languages/
├── en.json
├── tr.json
├── de.json
... (36 language files)
```

### 🔧 Code Quality
- Separation of concerns: data (JSON) separated from logic (PHP)
- Per-language caching for better performance
- Added `clear_cache()` method for memory management

### 📋 Full Changelog
See [CHANGELOG.md](https://github.com/ercanatay/yt-consent-translations/blob/main/changelog.md) for complete details.

---

**Full Changelog**: https://github.com/ercanatay/yt-consent-translations/compare/v1.1.0...v1.2.0
