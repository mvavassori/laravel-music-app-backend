<?php

namespace App\Repositories;

use App\Models\Role;
use App\Contracts\Repositories\RoleRepositoryInterface;

class MySQLRoleRepository implements RoleRepositoryInterface {
    public function create(array $data) {
        return Role::create($data);
    }
    public function find($id) {
        return Role::findOrFail($id);
    }
    public function index() {
        return Role::all();
    }
    public function findByName($name) {
        return Role::where('name', $name)->first();
    }
    public function delete($id) {
        return Role::destroy($id);
    }
}