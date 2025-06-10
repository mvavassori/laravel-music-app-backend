<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Models\Role;
use App\Services\RoleService;

class RoleController extends Controller {
    private RoleService $roleService;

    public function __construct(RoleService $roleService) {
        $this->roleService = $roleService;
    }
    public function store(RoleStoreRequest $request) {
        $role = $this->roleService->createRole($request->validated());
        return response()->json($role, 201);
    }

    public function index() {
        $roles = $this->roleService->getAllRoles();
        return response()->json($roles, 200);
    }

    public function show($id) {
        $role = $this->roleService->getRole($id);
        return response()->json($role, 200);
    }

    public function showByName($name) {
        $role = $this->roleService->getRoleByName($name);
        return response()->json($role, 200);
    }

    public function destroy($id) {
        $role = Role::findOrFail($id);
        $this->roleService->deleteRole($role);

        return response()->noContent(204);
    }
}
