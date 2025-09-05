<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class SkillsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('seed/skills.csv');
        if (!file_exists($path)) return;

        $csv = Reader::createFromPath($path);
        $csv->setHeaderOffset(0);
        $rows = collect(iterator_to_array($csv->getRecords()));

        // First pass: create parents
        $bySlug = [];
        foreach ($rows as $r) {
            $bySlug[$r['slug']] = Skill::firstOrCreate(
                ['slug' => $r['slug']],
                ['name'=>$r['name'],'description'=>$r['description'] ?? null,'type'=>$r['type'] ?? 'technical','level_scale'=>$r['level_scale'] ?: 5]
            );
        }
        // Second pass: set parent_id
        foreach ($rows as $r) {
            if (!empty($r['parent_slug'])) {
                $child = $bySlug[$r['slug']];
                $parent = $bySlug[$r['parent_slug']] ?? null;
                if ($parent && $child->parent_id !== $parent->id) {
                    $child->parent_id = $parent->id; $child->save();
                }
            }
        }
    }
}
