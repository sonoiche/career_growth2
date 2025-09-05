# Skills Taxonomy & Role–Skill Mapping (Laravel 11)

## Environment & Versions
- PHP: 8.3
- Laravel: 11.x
- Composer: 2.7+
- DB: MySQL 8.0+ (MariaDB 10.6+ acceptable)
- Cache: Redis 7 (preferred; file cache fallback works)
- OS/Runtime: macOS/Linux or Docker

No third-party credentials are required. Everything runs locally/offline.


## Quickstart

```bash
cd career_growth2
php artisan migrate
composer require league/csv --dev
php artisan db:seed
php artisan serve
```

The API will be available at `http://127.0.0.1:8000/api`

# cURL Examples
List skills (with filters + pagination)
```
curl -i "http://127.0.0.1:8000/api/skills?type=technical&q=react&page=1&per_page=20"
```
Get a skill by ID
```
curl -i "http://127.0.0.1:8000/api/skills/42"
```
List roles (with filters)
```
curl -i "http://127.0.0.1:8000/api/roles?family=Engineering&seniority=mid&q=frontend"
```
Get a role by ID
```
curl -i "http://127.0.0.1:8000/api/roles/5"
```
Get required skills for a role (ID or slug)
```
curl -i "http://127.0.0.1:8000/api/roles/se-2/skills"
```

## Normalized Enums / Allowed Values

# Skill Types
Backed by skill_types lookup table. Keys (case-insensitive in requests; stored normalized as lowercase):
- technical
- soft

Validation (request rules):
```
use Illuminate\Validation\Rule;
Rule::in(['technical','soft'])
```
DB enforcement: foreign key from skills.type_id → skill_types.id.

## Role Seniority

Normalized to these lowercase keys (use in requests; stored normalized):
- intern, junior, mid, senior, staff, principal, lead, manager

# Validation (request rules):
```
Rule::in(['intern','junior','mid','senior','staff','principal','lead','manager'])
```
# Optional DB check (MySQL 8+):
Add a CHECK constraint in the roles migration (or create a role_seniorities lookup if you prefer):
```
DB::statement("ALTER TABLE roles
  ADD CONSTRAINT roles_seniority_chk
  CHECK (LOWER(seniority) IN ('intern','junior','mid','senior','staff','principal','lead','manager'))");
```
# Request normalization helper (example):
```
function normKey(?string $v): ?string {
    return $v ? strtolower(trim($v)) : null;
}
```

## Resource Safety: Don’t Assume Loaded Relations
RoleSkillResource (defensive against pivot/missing relations)

Works whether you pass a RoleSkill model or a Skill model with a pivot (from belongsToMany), and it won’t explode if relations weren’t eager-loaded.
```
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleSkillResource extends JsonResource
{
    public function toArray($request)
    {
        // Support both styles:
        // 1) Resource wraps a RoleSkill model (has direct columns)
        // 2) Resource wraps a Skill model attached via pivot
        $pivot = $this->pivot ?? null;

        $requiredLevel = $this->when(
            ($pivot && isset($pivot->required_level)) || isset($this->required_level),
            fn () => (int) ($pivot->required_level ?? $this->required_level ?? 0)
        );

        $weight = $this->when(
            ($pivot && isset($pivot->weight)) || isset($this->weight),
            fn () => (float) ($pivot->weight ?? $this->weight ?? 1.0)
        );

        $isRequired = $this->when(
            ($pivot && isset($pivot->is_required)) || isset($this->is_required),
            fn () => (bool) ($pivot->is_required ?? $this->is_required ?? false)
        );

        // Safely include skill and role if present
        return [
            'skill' => new SkillResource($this->whenLoaded('skill', $this->skill ?? $this)),
            'role'  => new RoleResource($this->whenLoaded('role')),

            'required_level' => $requiredLevel,
            'weight'         => $weight,
            'is_required'    => $isRequired,
        ];
    }
}
```
# Controller tip:

- If you return RoleSkillResource::collection($role->roleSkills()->with('skill')->paginate()), you’re wrapping RoleSkill rows—no pivot usage.
- If you return RoleSkillResource::collection($role->skills()->withPivot(...)->paginate()), you’re wrapping Skill models—pivot is used.

The resource above safely handles both.

## Request Validation (normalized & guarded)

IndexSkillsRequest example:
```
public function rules(): array
{
    return [
        'parent_id' => ['nullable','integer','exists:skills,id'],
        'type'      => ['nullable','string', Rule::in(['technical','soft'])],
        'q'         => ['nullable','string','max:100'],
        'page'      => ['nullable','integer','min:1'],
        'per_page'  => ['nullable','integer','min:1','max:100'],
    ];
}

protected function prepareForValidation(): void
{
    $this->merge([
        'type' => $this->type ? strtolower(trim($this->type)) : null,
        'q'    => $this->q ? trim($this->q) : null,
    ]);
}
```

IndexRolesRequest example:
```
public function rules(): array
{
    return [
        'family'    => ['nullable','string','max:100'],
        'seniority' => ['nullable','string', Rule::in(['intern','junior','mid','senior','staff','principal','lead','manager'])],
        'q'         => ['nullable','string','max:100'],
        'page'      => ['nullable','integer','min:1'],
        'per_page'  => ['nullable','integer','min:1','max:100'],
    ];
}

protected function prepareForValidation(): void
{
    $this->merge([
        'seniority' => $this->seniority ? strtolower(trim($this->seniority)) : null,
        'family'    => $this->family ? trim($this->family) : null,
        'q'         => $this->q ? trim($this->q) : null,
    ]);
}
```

## Seeders: Robust Booleans & Quoting
Goal: is_required should accept true/false/1/0/yes/no/y/n (any case), and quoted CSV cells should parse cleanly.

# Boolean parser helper
Create app/Support/CsvBool.php:
```
<?php

namespace App\Support;

final class CsvBool
{
    public static function parse(mixed $v, bool $default=false): bool
    {
        if ($v === null) return $default;

        if (is_bool($v)) return $v;
        if (is_int($v)) return $v === 1;

        $s = strtolower(trim((string)$v));
        if ($s === '') return $default;

        return in_array($s, ['1','true','t','yes','y'], true)
            || (is_numeric($s) && (int)$s === 1);
    }
}
```

# Seeder usage (example from RoleSkillsSeeder)
```
use League\Csv\Reader;
use App\Support\CsvBool;

$csv = Reader::createFromPath(base_path('seed/role_skills.csv'), 'r');
$csv->setHeaderOffset(0);

foreach ($csv->getRecords() as $row) {
    $role = \App\Models\Role::where('slug', trim($row['role_slug']))->firstOrFail();
    $skill = \App\Models\Skill::where('slug', trim($row['skill_slug']))->firstOrFail();

    \App\Models\RoleSkill::updateOrCreate(
        ['role_id' => $role->id, 'skill_id' => $skill->id],
        [
            'required_level' => (int) $row['required_level'],
            'weight'         => (float) $row['weight'],
            'is_required'    => CsvBool::parse($row['is_required'], true),
        ]
    );
}
```

## Repository Layout

```text
api/
  openapi.yaml
  examples/
  postman_collection.json
app/
  Domain/Skills/{AliasResolver.php,PrerequisiteValidator.php}
  Http/{Controllers,Middleware,Requests,Resources}
  Models/{Role.php,RoleSkill.php,Skill.php,SkillAlias.php,Progression.php}
  Providers/
  Support/CsvBool.php
config/
database/{migrations,seeders}
docs/{deploy.md,skills-mapping.md,governance.md,data-quality.md}
routes/
seed/{skills.csv,skills.json,roles.csv,role_skills.csv,progressions.csv,skill_aliases.csv}
tests/{Feature,Unit}
composer.json
README.md
```

## Endpoints

- GET /api/skills
- GET /api/skills/{id}
- GET /api/roles
- GET /api/roles/{id}
- GET /api/roles/{role}/skills

## How It Works (Testing with Postman)

1. Import the API Collection
    - Open Postman.
    - Click Import and select the file /api/postman_collection.json included in this package.
    - This will load all available API endpoints with example requests.

2. Available Endpoints
    - GET /api/skills → List skills (supports filters: parent_id, type, q).
    - GET /api/skills/{id} → Show details for a single skill.
    - GET /api/roles → List roles (supports filters: family, seniority, q).
    - GET /api/roles/{id} → Show details for a single role.
    - GET /api/roles/{role}/skills → Show all skills required for a specific role.

3. Example: Fetch all skills
    - In Postman, select the GET /api/skills request.
    - Click Send.
    - You should see a JSON response containing skill records, pagination info, and the taxonomy version in the headers (X-Taxonomy-Version).

4. Example: Fetch skills for a specific role
    - In Postman, select GET /api/roles/{role}/skills.
    - Replace {role} in the URL with a valid role ID or slug (from the /roles endpoint).
    - Click Send to retrieve all required skills, including required_level, weight, and is_required.

5. Filtering Skills
    - Use query parameters to refine results:

```
GET /api/skills?type=technical&q=react
```

This example fetches all technical skills that match “react” in their name or slug.