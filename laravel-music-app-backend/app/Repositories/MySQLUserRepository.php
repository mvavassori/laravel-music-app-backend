<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class MySQLUserRepository implements UserRepositoryInterface {
    public function find($id) {
        return User::findOrFail($id);
    }
    public function index() {
        return User::all();
    }
    public function create(array $data, $hashedPassword) {
        return User::create([
            'name'=> $data['name'],
            'email'=> $data['email'],
            'password'=> $hashedPassword,
        ]);
    }
}