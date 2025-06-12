<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;

class UserService implements UserServiceInterface {
    private UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }
    public function getAllUsers() {
        return $this->userRepository->index();
    }

    public function getUser($id) {
        return $this->userRepository->find($id);
    }

    public function createUser($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        return $this->userRepository->create($data, $hashedPassword);
    }
}