<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Skill extends Model
{
    protected $fillable = ['slug','name','description','type','level_scale','parent_id'];

    public function parent(): BelongsTo { return $this->belongsTo(Skill::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(Skill::class, 'parent_id'); }
    public function aliases(): HasMany { return $this->hasMany(SkillAlias::class); }
    public function prerequisites(): BelongsToMany {
        return $this->belongsToMany(Skill::class, 'skill_prerequisites', 'skill_id', 'prerequisite_skill_id');
    }
    public function requiredByRoles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'role_skills')->withPivot(['required_level','weight','is_required']);
    }

    /* Scopes */
    public function scopeOfType($q, ?string $type) { return $type ? $q->where('type',$type) : $q; }
    public function scopeSearch($q, ?string $term) {
        return $term ? $q->where(fn($qq)=>$qq->where('name','like',"%$term%")->orWhere('slug','like',"%$term%")) : $q;
    }
    public function scopeRoots($q) { return $q->whereNull('parent_id'); }
}
