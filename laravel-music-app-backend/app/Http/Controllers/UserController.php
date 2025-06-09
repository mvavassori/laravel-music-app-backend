<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function show($id) {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|max:255'
        ]);

        $hashedPassword = password_hash($validated['password'], PASSWORD_BCRYPT);

        $user = User::create([
            'name'=> $validated['name'],
            'email'=> $validated['email'],
            'password'=> $hashedPassword,
        ]);

        return response()->json($user, 201);
    }
}
