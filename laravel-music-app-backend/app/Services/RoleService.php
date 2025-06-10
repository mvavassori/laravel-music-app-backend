<?php

namespace App\Services;

use App\Models\Role;

class RoleService {
    public function createRole($data) {
        $role = Role::create($data);
        return $role;
    }

    public function getAllRoles() {
        $roles = Role::all();
        return $roles;
    }

    public function getRole($id) {
        $role = Role::findOrFail($id);
        return $role;
    }

    public function getRoleByName($name) {
        $role = Role::where('name', $name)->first();
        return $role;
    }

    public function deleteRole(Role $role) {
        $role->delete();
    }
}