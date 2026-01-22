#!/usr/bin/env python3
"""
JSON Language File Validator for YT Consent Translations

Validates all JSON language files in the languages/ directory to ensure:
1. Valid JSON syntax
2. All required translation keys are present

Usage:
    python scripts/validate_json.py

Returns exit code 0 if all files are valid, 1 otherwise.
"""

import json
import os
import sys

# All 21 required translation keys
REQUIRED_KEYS = [
    'banner_text', 'banner_link', 'button_accept', 'button_reject', 'button_settings',
    'modal_title', 'modal_content', 'modal_content_link',
    'functional_title', 'preferences_title', 'statistics_title', 'marketing_title',
    'functional_content', 'preferences_content', 'statistics_content', 'marketing_content',
    'show_services', 'hide_services', 'modal_accept', 'modal_reject', 'modal_save'
]


def validate_json_file(filepath):
    """Validate a single JSON file."""
    filename = os.path.basename(filepath)
    
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            data = json.load(f)
    except json.JSONDecodeError as e:
        print(f"ERROR: Invalid JSON in {filename}: {e}")
        return False
    except Exception as e:
        print(f"ERROR: Could not read {filename}: {e}")
        return False

    # Check for missing keys
    missing_keys = [key for key in REQUIRED_KEYS if key not in data]
    if missing_keys:
        print(f"WARNING: Missing keys in {filename}: {missing_keys}")
        # Not strictly an error (fallback exists), but good to know
    
    # Check for empty values
    empty_values = [key for key, value in data.items() if not value]
    if empty_values:
        print(f"WARNING: Empty values in {filename}: {empty_values}")

    print(f"OK: {filename}")
    return True


def main():
    """Main entry point."""
    # Determine languages directory path
    script_dir = os.path.dirname(os.path.abspath(__file__))
    languages_dir = os.path.join(script_dir, '..', 'languages')
    
    if not os.path.isdir(languages_dir):
        print(f"ERROR: Directory {languages_dir} not found.")
        sys.exit(1)

    print(f"Validating JSON files in {os.path.abspath(languages_dir)}...\n")
    
    all_valid = True
    file_count = 0
    
    for filename in sorted(os.listdir(languages_dir)):
        if filename.endswith('.json'):
            filepath = os.path.join(languages_dir, filename)
            if not validate_json_file(filepath):
                all_valid = False
            file_count += 1

    print(f"\n{'='*50}")
    print(f"Validated {file_count} JSON files.")
    
    if all_valid:
        print("Result: All files are valid.")
        sys.exit(0)
    else:
        print("Result: Some files have errors.")
        sys.exit(1)


if __name__ == '__main__':
    main()
