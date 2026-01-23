#!/usr/bin/env python3
"""
Code Quality & Security Scanner for YT Consent Translations
"""

import os
import re
import sys

# Configuration
PLUGIN_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PHP_EXTENSIONS = ['.php']
EXCLUDE_DIRS = ['.git', 'node_modules', 'vendor', 'assets', 'languages', 'scripts']

# Regex Patterns
PATTERN_ECHO = re.compile(r'\b(echo|print|printf)\b\s*[^;]+;')
PATTERN_INPUT = re.compile(r'\$_(POST|GET|REQUEST)\[[\'"](\w+)[\'"]\]')
PATTERN_INDENTATION_SPACES = re.compile(r'^\t* (?!\*)')

def get_php_files(root_dir):
    php_files = []
    for root, dirs, files in os.walk(root_dir):
        # Exclude directories
        dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS]

        for file in files:
            if any(file.endswith(ext) for ext in PHP_EXTENSIONS):
                php_files.append(os.path.join(root, file))
    return php_files

def check_version_consistency():
    main_file = os.path.join(PLUGIN_DIR, 'yt-consent-translations.php')
    readme_file = os.path.join(PLUGIN_DIR, 'readme.txt')

    version_main = None
    version_readme = None

    if os.path.exists(main_file):
        with open(main_file, 'r', encoding='utf-8') as f:
            content = f.read()
            match = re.search(r'Version:\s*([0-9.]+)', content)
            if match:
                version_main = match.group(1)

    if os.path.exists(readme_file):
        with open(readme_file, 'r', encoding='utf-8') as f:
            content = f.read()
            match = re.search(r'Stable tag:\s*([0-9.]+)', content)
            if match:
                version_readme = match.group(1)

    if version_main and version_readme:
        if version_main == version_readme:
            print(f"[OK] Version consistency: {version_main}")
        else:
            print(f"[FAIL] Version mismatch: Main={version_main}, Readme={version_readme}")
    else:
        print(f"[WARN] Could not find versions. Main={version_main}, Readme={version_readme}")

def find_balanced_parentheses(text, start_index):
    """Find the content inside balanced parentheses starting at start_index."""
    depth = 0
    end_index = -1
    for i in range(start_index, len(text)):
        char = text[i]
        if char == '(':
            depth += 1
        elif char == ')':
            depth -= 1
            if depth == 0:
                end_index = i
                break
    return end_index

def check_file_content(filepath):
    issues = []
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            lines = f.readlines()
            content = ''.join(lines)

        rel_path = os.path.relpath(filepath, PLUGIN_DIR)

        # 1. Indentation
        for i, line in enumerate(lines):
            if PATTERN_INDENTATION_SPACES.match(line):
                 issues.append(f"Line {i+1}: Indentation uses spaces instead of tabs")
                 break # Report once per file

        # 2. Strict in_array & Performance
        search_start = 0
        while True:
            match = re.search(r'\bin_array\s*\(', content[search_start:])
            if not match:
                break

            match_index = search_start + match.start()
            open_paren_index = search_start + match.end() - 1

            end_index = find_balanced_parentheses(content, open_paren_index)

            if end_index != -1:
                args_content = content[open_paren_index+1:end_index]
                # Strict check
                if 'true' not in args_content.lower():
                     issues.append(f"Potential non-strict in_array (Security): in_array({args_content[:50]}...)")

                # Performance warning
                issues.append(f"Performance: Consider using isset() instead of in_array() at line {content[:match_index].count(chr(10))+1}")

            search_start = match_index + 1

        # 3. Input Sanitization
        matches = PATTERN_INPUT.finditer(content)
        for match in matches:
            full_match = match.group(0)
            start = match.start()
            preceding = content[max(0, start-50):start]
            # Naive check for sanitization or verification
            if not re.search(r'sanitize_\w+\s*\(\s*.*$', preceding, re.DOTALL) and 'wp_verify_nonce' not in preceding:
                 is_check = False
                 if re.search(r'(isset|empty)\s*\(\s*.*$', preceding, re.DOTALL):
                     is_check = True
                 if not is_check:
                     issues.append(f"Direct access to input without immediate sanitization: {full_match}")

        # 4. Unescaped Echo
        matches = re.finditer(r'\becho\s+(\$[a-zA-Z0-9_]+)', content)
        for match in matches:
            start = match.start()
            # Check if likely safe (ternary, assignment)
            end = match.end()
            next_chars = content[end:end+20].strip()
            if next_chars.startswith('?') or next_chars.startswith('=') or next_chars.startswith('<') or next_chars.startswith('>'):
                pass
            else:
                issues.append(f"Potential unescaped output: {match.group(0)}")

    except Exception as e:
        issues.append(f"Error reading file: {e}")

    if issues:
        print(f"\nIssues in {rel_path}:")
        for issue in issues:
            print(f"  - {issue}")

def main():
    print("Starting Code Scan...")
    check_version_consistency()

    php_files = get_php_files(PLUGIN_DIR)
    for file in php_files:
        check_file_content(file)

    print("\nScan complete.")

if __name__ == "__main__":
    main()
