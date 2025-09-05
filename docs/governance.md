# Data Governance & Versioning

## Core Principles
- **No hard deletes** for taxonomy tables (`skills`, `roles`, mappings). Use soft deletes or `deprecated_at` columns if needed.
- **Explicit versioning**: bump `TAXONOMY_VERSION` after any schema or data change that could affect consumers.
- **Reproducibility**: all seed data and diagrams are versioned in-repo; no external links.

## Change Types
- **Patch**: description or metadata change; bump N (e.g., `2025.08.14.2` → `.3`).
- **Minor**: new skills/roles or non-breaking constraints; bump date or N.
- **Major**: breaking changes (renames of slugs, removing fields); communicate in release notes and bump date.

## Deprecations
- Mark records deprecated; keep aliases resolving to canonical skills.
- Provide a migration path: add new canonical entries, retain old slugs as aliases, and redirect lookups.

## Reviews & Approval
- Schema changes require PR with ERD update (`docs/erd.mmd` + `erd.png`) and test coverage.
- Imports must pass data-quality checks (see `docs/data-quality.md`).

## Auditability
- Keep JSON logs; include `X-Request-Id` in all API responses.
- Store import summaries in `/storage/app/reports/import-summary.json`.
