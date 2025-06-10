<?php

namespace App\Services;

use App\Models\User;

class UserService {
    public function getAllUsers() {
        $users = User::all();
        return $users;
    }

    public function getUser($id) {
        $user = User::findOrFail($id);
        return $user;
    }

    public function createUser($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = User::create([
            'name'=> $data['name'],
            'email'=> $data['email'],
            'password'=> $hashedPassword,
        ]);
        return $user;
    }
}