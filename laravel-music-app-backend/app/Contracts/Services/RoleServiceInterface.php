<?php

namespace App\Contracts\Services;

interface RoleServiceInterface {
    public function createRole($data);
    public function getAllRoles();
    public function getRole($id);
    public function getRoleByName($name);
    public function deleteRole($id) ;
}