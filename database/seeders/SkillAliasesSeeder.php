<?php

namespace Database\Seeders;

use App\Models\{Skill, SkillAlias};
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class SkillAliasesSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('seed/skill_aliases.csv');
        if (!file_exists($path)) return;

        $csv = Reader::createFromPath($path);
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $r) {
            $skill = Skill::where('slug',$r['skill_slug'])->first();
            if ($skill) {
                SkillAlias::firstOrCreate(['skill_id'=>$skill->id, 'alias'=>$r['alias']]);
            }
        }
    }
}
