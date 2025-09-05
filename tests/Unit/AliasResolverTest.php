<?php

use App\Domain\Skills\AliasResolver;
use App\Models\{Skill, SkillAlias};

it('resolves by slug, name, and alias', function () {
    $skill = Skill::create(['slug'=>'javascript','name'=>'JavaScript','type'=>'technical','level_scale'=>5]);
    SkillAlias::create(['skill_id'=>$skill->id,'alias'=>'JS']);

    $resolver = new AliasResolver();

    expect($resolver->canonicalSkillId('javascript'))->toBe($skill->id);
    expect($resolver->canonicalSkillId('JavaScript'))->toBe($skill->id);
    expect($resolver->canonicalSkillId('JS'))->toBe($skill->id);
});

it('throws on unknown alias', function () {
    $resolver = new AliasResolver();
    $resolver->canonicalSkillId('unknown-alias');
})->throws(InvalidArgumentException::class);
