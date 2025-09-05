<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleSkillResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'skill' => new SkillResource($this->whenLoaded('skill')),
            'required_level' => (int)$this->pivot->required_level,
            'weight' => (float)$this->pivot->weight,
            'is_required' => (bool)$this->pivot->is_required,
        ];
    }
}
