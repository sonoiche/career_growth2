<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('seed/roles.csv');
        if (!file_exists($path)) return;

        $csv = Reader::createFromPath($path);
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $r) {
            Role::firstOrCreate(['slug'=>$r['slug']], [
                'title'=>$r['title'],
                'family'=>$r['family'] ?? null,
                'seniority'=>$r['seniority'] ?? null,
                'description'=>$r['description'] ?? null,
            ]);
        }
    }
}
