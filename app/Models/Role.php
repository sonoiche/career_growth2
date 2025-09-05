<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};

class Role extends Model
{
    protected $fillable = ['slug','title','family','seniority','description'];

    public function skills(): BelongsToMany {
        return $this->belongsToMany(Skill::class, 'role_skills')->withPivot(['required_level','weight','is_required']);
    }
    public function fromProgressions(): HasMany { return $this->hasMany(Progression::class,'from_role_id'); }
    public function toProgressions(): HasMany { return $this->hasMany(Progression::class,'to_role_id'); }

    public function scopeFamily($q, ?string $family) { return $family ? $q->where('family',$family) : $q; }
    public function scopeSeniority($q, ?string $seniority) { return $seniority ? $q->where('seniority',$seniority) : $q; }
    public function scopeSearch($q, ?string $term) { return $term ? $q->where('title','like',"%$term%") : $q; }
}
