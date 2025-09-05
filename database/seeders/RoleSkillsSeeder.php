<?php

namespace Database\Seeders;

use App\Models\{Role, Skill};
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class RoleSkillsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('seed/role_skills.csv');
        if (!file_exists($path)) return;

        $csv = Reader::createFromPath($path);
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $r) {
            $role = Role::where('slug',$r['role_slug'])->firstOrFail();
            $skill = Skill::where('slug',$r['skill_slug'])->firstOrFail();

            DB::table('role_skills')->updateOrInsert(
                ['role_id'=>$role->id, 'skill_id'=>$skill->id],
                [
                    'required_level' => (int)$r['required_level'],
                    'weight' => (float)$r['weight'],
                    'is_required' => filter_var($r['is_required'], FILTER_VALIDATE_BOOLEAN),
                ]
            );
        }
    }
}
