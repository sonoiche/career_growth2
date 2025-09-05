# Skills Taxonomy & Role–Skill Mapping

This document defines entities, rules, and API behavior for the taxonomy and mappings.

## Entities (overview)
- **Skills**: canonical entries with `name`, `slug`, `description`, `type` (FK to `skill_types`), optional `parent_id` for hierarchy.
- **Skill Aliases**: alternate names (globally unique, case-insensitive), each maps to exactly one skill.
- **Skill Prerequisites**: DAG edges `prerequisite_skill_id -> skill_id`; no cycles, self-edges, duplicates, or ancestor/descendant edges.
- **Roles**: job titles with `slug`, `title`, `family`, `seniority` (normalized key).
- **Role Skills**: mapping with `required_level`, `weight`, `is_required`.
- **Progressions**: directed `from_role_id -> to_role_id`, unique per pair; acyclic within a family path.

## Normalized Keys
- `skill_types.key`: `technical`, `soft`
- `roles.seniority`: `intern`, `junior`, `mid`, `senior`, `staff`, `principal`, `lead`, `manager`

## Data Rules
- **Aliases**: global uniqueness; importer rejects duplicates with explicit error.
- **Prerequisites**: must form a DAG; forbidden edges: self, duplicate, ancestor/descendant; cross-type allowed.
- **Progressions**: unique per pair; 0.00–100.00 range for `min_score_to_progress`.

## API Behavior
- All `/skills*` and `/roles*` endpoints return paginated JSON and include:
  `X-Taxonomy-Version: <YYYY.MM.DD.N>`
- Filters:
  - `/skills`: `parent_id`, `type`, `q`
  - `/roles`: `family`, `seniority`, `q`
- `GET /roles/{role}/skills` returns `{ skill, required_level, weight, is_required }` per item.

See `docs/rules-overview.mmd` for a visual summary; render to PNG and include as `docs/rules-overview.png`.