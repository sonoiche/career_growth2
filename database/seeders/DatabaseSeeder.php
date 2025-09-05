<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SkillsSeeder::class,
            RolesSeeder::class,
            RoleSkillsSeeder::class,
            SkillAliasesSeeder::class,
            ProgressionsSeeder::class,
        ]);
    }
}
