<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class ProgressionsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('seed/progressions.csv');
        if (!file_exists($path)) return;

        $csv = Reader::createFromPath($path);
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $r) {
            $from = Role::where('slug',$r['from_role_slug'])->first();
            $to = Role::where('slug',$r['to_role_slug'])->first();
            if ($from && $to) {
                DB::table('progressions')->updateOrInsert(
                    ['from_role_id'=>$from->id,'to_role_id'=>$to->id],
                    ['rationale'=>$r['rationale'] ?? null, 'min_score_to_progress'=>(float)($r['min_score_to_progress'] ?? 0)]
                );
            }
        }
    }
}
