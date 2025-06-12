<?php

namespace App\Services;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Services\RoleServiceInterface;

class RoleService implements RoleServiceInterface {
    private RoleRepositoryInterface $roleRepository;
    public function __construct(RoleRepositoryInterface $roleRepository) {
        $this->roleRepository = $roleRepository;
    }
    public function createRole($data) {
        return $this->roleRepository->create($data);
    }
    public function getAllRoles() {
        return $this->roleRepository->index();
    }
    public function getRole($id) {
        return $this->roleRepository->find($id);
    }
    public function getRoleByName($name) {
        return $this->roleRepository->findByName($name);
    }
    public function deleteRole($id) {
        return $this->roleRepository->delete($id);
    }
}