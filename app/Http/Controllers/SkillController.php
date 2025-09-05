<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSkillsRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index(IndexSkillsRequest $request)
    {
        $q = Skill::query()
            ->when($request->parent_id, fn($qq)=>$qq->where('parent_id',$request->parent_id))
            ->ofType($request->type)
            ->search($request->q)
            ->orderBy('name');

        $skills = $q->paginate($request->integer('per_page', 15));
        return SkillResource::collection($skills)->additional([
            'meta' => ['version' => config('app.taxonomy_version', env('TAXONOMY_VERSION','dev'))]
        ]);
    }

    public function show(Skill $skill)
    {
        return (new SkillResource($skill))
            ->additional(['meta' => ['version' => config('app.taxonomy_version', env('TAXONOMY_VERSION','dev'))]]);
    }
}
