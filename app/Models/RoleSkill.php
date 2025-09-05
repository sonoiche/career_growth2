<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleSkill extends Pivot
{
    protected $table = 'role_skills';
    public $timestamps = false;
    protected $fillable = ['role_id','skill_id','required_level','weight','is_required'];

    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function skill(): BelongsTo { return $this->belongsTo(Skill::class); }
}
