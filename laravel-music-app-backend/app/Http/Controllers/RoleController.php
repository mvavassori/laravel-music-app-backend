<?php

namespace App\Http\Controllers;

use App\Contracts\Services\RoleServiceInterface;
use App\Http\Requests\RoleStoreRequest;

class RoleController extends Controller {
    private RoleServiceInterface $roleService;

    public function __construct(RoleServiceInterface $roleService) {
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
        $this->roleService->deleteRole($id);
        return response()->noContent(204);
    }
}
