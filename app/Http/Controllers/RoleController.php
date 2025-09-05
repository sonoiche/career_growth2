<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRolesRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(IndexRolesRequest $request)
    {
        $q = Role::query()
            ->family($request->family)
            ->seniority($request->seniority)
            ->search($request->q)
            ->orderBy('title');

        return RoleResource::collection($q->paginate($request->integer('per_page', 15)));
    }

    public function show(Role $role)
    {
        return new RoleResource($role);
    }
}
