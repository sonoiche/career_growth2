<?php

use App\Models\Skill;

it('lists skills with pagination and filtering', function () {
    Skill::factory()->create(['slug'=>'a','name'=>'Alpha','type'=>'technical']);
    Skill::factory()->create(['slug'=>'b','name'=>'Beta','type'=>'soft']);

    $resp = $this->getJson('/api/skills?type=technical');
    $resp->assertOk()->assertJsonPath('data.0.type','technical');
});
