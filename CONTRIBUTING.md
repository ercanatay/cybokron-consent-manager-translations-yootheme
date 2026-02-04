# Contributing to YT Consent Translations

Thank you for your interest in contributing! Here's how you can help.

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/ercanatay/yt-consent-translations/issues)
2. If not, create a new issue with:
   - Clear description of the problem
   - Steps to reproduce
   - Expected vs actual behavior
   - WordPress and PHP versions

### Adding New Languages

1. Fork the repository
2. Edit `includes/class-strings.php`:
   - Add your language to the `$languages` array
   - Add your locale mapping to the `$locale_map` array
3. Create a new JSON file in `languages/` (e.g., `fr.json` for French):
   - Copy `languages/en.json` as a template
   - Translate all values
4. Run `python scripts/validate_json.py` to verify your JSON file
5. Submit a Pull Request

### Code Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Use meaningful commit messages
- Test your changes before submitting

## Pull Request Process

1. Fork and create a feature branch
2. Make your changes
3. Test thoroughly
4. Submit PR with clear description

## Questions?

Open an issue or contact [@ercanatay](https://github.com/ercanatay)
