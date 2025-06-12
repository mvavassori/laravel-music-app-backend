<?php

namespace App\Contracts\Services;

use App\Models\User;

interface UserServiceInterface {
    public function getAllUsers();
    public function getUser($id);
    public function createUser($data);
}