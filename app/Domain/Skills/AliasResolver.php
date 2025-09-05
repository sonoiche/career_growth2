<?php

namespace App\Domain\Skills;

use App\Models\{Skill, SkillAlias};
use Illuminate\Support\Str;
use InvalidArgumentException;

class AliasResolver
{
    public function canonicalSkillId(string|int $input): int
    {
        if (is_int($input) || ctype_digit((string)$input)) {
            $skill = Skill::find((int)$input);
            if ($skill) return $skill->id;
        }

        $needle = Str::lower(trim((string)$input));

        $bySlug = Skill::whereRaw('LOWER(slug) = ?', [$needle])->first();
        if ($bySlug) return $bySlug->id;

        $byName = Skill::whereRaw('LOWER(name) = ?', [$needle])->first();
        if ($byName) return $byName->id;

        $aliases = SkillAlias::whereRaw('LOWER(alias) = ?', [$needle])->pluck('skill_id')->unique();
        if ($aliases->count() === 1) return (int)$aliases->first();
        if ($aliases->count() > 1) {
            throw new InvalidArgumentException("Ambiguous alias '{$input}' maps to multiple skills.");
        }

        throw new InvalidArgumentException("Unknown skill '{$input}'.");
    }

    /** @param array<int,string|int> $inputs */
    public function resolveMany(array $inputs): array
    {
        return array_map(fn($v) => $this->canonicalSkillId($v), $inputs);
    }
}
