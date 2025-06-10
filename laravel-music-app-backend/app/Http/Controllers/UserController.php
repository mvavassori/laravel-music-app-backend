<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Services\UserService;

class UserController extends Controller {

    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function index() {
        $users = $this->userService->getAllUsers();
        return response()->json($users, 200);
    }

    public function show($id) {
        $user = $this->userService->getUser($id);
        return response()->json($user, 200);
    }

    public function store(UserStoreRequest $request) {
        $user = $this->userService->createUser($request->validated());
        return response()->json($user, 201);
    }
}
