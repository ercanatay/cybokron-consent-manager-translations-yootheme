#!/usr/bin/env python3
"""
Fix Indentation Script
Converts 4 spaces to tabs for indentation in PHP files.
"""

import os
import re

PLUGIN_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PHP_EXTENSIONS = ['.php']
EXCLUDE_DIRS = ['.git', 'node_modules', 'vendor', 'assets', 'languages', 'scripts']

def get_php_files(root_dir):
    php_files = []
    for root, dirs, files in os.walk(root_dir):
        # Exclude directories
        dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS]

        for file in files:
            if any(file.endswith(ext) for ext in PHP_EXTENSIONS):
                php_files.append(os.path.join(root, file))
    return php_files

def fix_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            lines = f.readlines()

        new_lines = []
        changed = False

        for line in lines:
            original_line = line

            # Loop to replace 4 spaces with a tab at the beginning of the line
            # It also handles mixed indentation: \t    -> \t\t
            while True:
                # Regex matches start of line, optional tabs, then 4 spaces
                # We group the tabs so we can keep them
                match = re.match(r'^(\t*)    ', line)
                if match:
                    # Replace with tabs + tab + rest
                    # match.group(0) is \t...
                    # match.group(1) is \t...
                    tabs = match.group(1)
                    line = tabs + '\t' + line[len(match.group(0)):]
                else:
                    break

            if line != original_line:
                changed = True

            new_lines.append(line)

        if changed:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.writelines(new_lines)
            print(f"Fixed: {os.path.relpath(filepath, PLUGIN_DIR)}")
        else:
            # print(f"No changes: {os.path.relpath(filepath, PLUGIN_DIR)}")
            pass

    except Exception as e:
        print(f"Error processing {filepath}: {e}")

def main():
    files = get_php_files(PLUGIN_DIR)
    for file in files:
        fix_file(file)

if __name__ == '__main__':
    main()
