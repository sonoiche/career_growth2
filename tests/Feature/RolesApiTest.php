<?php

use App\Models\{Role, Skill};
use Illuminate\Support\Facades\DB;

it('shows role and role skills', function () {
    $role = Role::create(['slug'=>'fe','title'=>'Frontend Engineer']);
    $skill = Skill::create(['slug'=>'javascript','name'=>'JavaScript','type'=>'technical','level_scale'=>5]);
    DB::table('role_skills')->insert(['role_id'=>$role->id,'skill_id'=>$skill->id,'required_level'=>3,'weight'=>1,'is_required'=>true]);

    $this->getJson("/api/roles/{$role->id}")->assertOk()->assertJsonPath('data.slug','fe');
    $this->getJson("/api/roles/{$role->id}/skills")->assertOk()->assertJsonStructure(['data']);
});
