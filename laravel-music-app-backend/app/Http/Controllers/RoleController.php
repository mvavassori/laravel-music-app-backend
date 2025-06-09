<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $role = Role::create(['name' => $validated['name']]);

        return response()->json($role, 201);
    }

    public function index() {
        $roles = Role::all();
        return response()->json($roles, 200);
    }

    public function show($id) {
        $role = Role::findOrFail($id);
        return response()->json($role, 200);
    }

    public function showByName($name) {
        $role = Role::where('name', $name)->first();
        return response()->json($role, 200);
    }

    public function destroy($id) {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->noContent(204);
    }
}
