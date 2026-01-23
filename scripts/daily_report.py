#!/usr/bin/env python3
"""
Daily Report Generator for YT Consent Translations
Runs validation and scan scripts, and generates a formatted report.
"""

import os
import subprocess
import datetime
import sys

SCRIPTS_DIR = os.path.dirname(os.path.abspath(__file__))
ROOT_DIR = os.path.dirname(SCRIPTS_DIR)

def run_script(script_name):
    script_path = os.path.join(SCRIPTS_DIR, script_name)
    try:
        # Run with python3
        result = subprocess.run(
            ['python3', script_path],
            capture_output=True,
            text=True,
            check=False
        )
        output = result.stdout
        if result.stderr:
            output += "\nErrors:\n" + result.stderr
        return output, result.returncode
    except Exception as e:
        return f"Error running {script_name}: {e}", 1

def main():
    print("Running Daily Review...")

    # 0. Auto-fix (Indentation)
    # We run this first so the scan checks the fixed code
    fix_out, fix_code = run_script('fix_indentation.py')

    # 1. JSON Validation
    json_out, json_code = run_script('validate_json.py')

    # 2. Code Scan (includes performance checks)
    scan_out, scan_code = run_script('scan_code.py')

    # Analyze results
    issues_found = 0
    auto_fixed = 0

    # Count fixes
    auto_fixed += fix_out.count("Fixed:")

    # Count JSON issues
    if "Result: All files are valid" not in json_out:
        issues_found += 1

    # Count Code issues
    scan_issues = scan_out.count("Issues in")
    issues_found += scan_issues

    # Format Report
    date_str = datetime.date.today().strftime('%Y-%m-%d')

    report = f"""
# Daily Report - {date_str}

## Summary
- **Issues found**: {issues_found}
- **Auto-fixed**: {auto_fixed}
- **Needs review**: {issues_found}

## Details

### Auto-Fixes (Indentation)
```
{fix_out.strip() or "No indentation fixes needed."}
```

### JSON Validation
```
{json_out.strip()}
```

### Code Quality & Security Scan
```
{scan_out.strip()}
```
"""

    print(report)

    # Save report to file
    with open(os.path.join(ROOT_DIR, 'daily_report.md'), 'w', encoding='utf-8') as f:
        f.write(report)

if __name__ == '__main__':
    main()
