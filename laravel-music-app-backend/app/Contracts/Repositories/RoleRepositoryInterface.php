<?php

namespace App\Contracts\Repositories;

interface RoleRepositoryInterface {
    public function create(array $data);
    public function find($id);
    public function index();
    public function findByName($name);
    public function delete($id);
}