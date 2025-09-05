<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progression extends Model
{
    public $timestamps = false;
    protected $fillable = ['from_role_id','to_role_id','rationale','min_score_to_progress'];

    public function fromRole(): BelongsTo { return $this->belongsTo(Role::class,'from_role_id'); }
    public function toRole(): BelongsTo { return $this->belongsTo(Role::class,'to_role_id'); }
}
