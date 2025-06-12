<?php

namespace App\Contracts\Repositories;

interface UserRepositoryInterface {
    public function find($id);
    public function index();
    public function create(array $data, $hashedPassword);
}