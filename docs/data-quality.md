# Data Quality & Validation

## Import Validation (CLI / CI)
1. **Aliases**: global uniqueness (case-insensitive). Reject duplicates with explicit messages.
2. **Prerequisites**: enforce DAG; block cycles, self-edges, ancestor/descendant edges, duplicates.
3. **Progressions**: unique per pair; block cycles within a family path; range-check `min_score_to_progress` (0–100).
4. **Enums**: normalize `skill_types.key` and `roles.seniority` to approved sets.

## Automated Checks (tests/Feature/DataQualityTest.php)
- Orphan subskills (parent not found)
- Duplicate aliases
- Roles without any required skills (at least one `is_required = true`)
- Invalid seniority/type keys

## Reporting
- Console output during import
- Logs at `storage/logs/import-*.log`
- Summary JSON at `storage/app/reports/import-summary.json` with counts, warnings, and errors.

## Fix Process
- Correct the source CSV/JSON in `/seed/`
- Re-run `php artisan import:taxonomy --from=./seed --validate-only`
- Once clean, run `php artisan import:taxonomy --from=./seed --truncate`
- Bump `TAXONOMY_VERSION` and commit
