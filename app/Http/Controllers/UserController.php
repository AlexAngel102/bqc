<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => "required|min:3|max:32",
            'email' => "required|email",
            'password' => "required|min:8"
        ]);
        $validated['password'] = Hash::make($validated['password']);
        try {
            User::create($validated);
        } catch (\Exception $exception) {
            error_log($exception);
            return response()->json(['error' => 'User already exists'], 403);
        }
        return response()->json("User created", 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => "min:3|max:32",
            'email' => "email",
            'password' => "min:8",
        ]);
        $request['password'] = (Hash::make($request->password));
        $user->fill($request->all())->save();
        return response()->json("User updated", 200);
    }

    public function destroy(Request $request, User $user)
    {
        $password = $request->password;
        $hash = $user->password;
        $chek = Hash::check($password, $hash);
        if ($chek) {
            $user->delete();
            return response()->json("User deleted", 200);
        }
        return response()->json(['error' => "Invalid data"], 400);
    }

    public function show(int $id)
    {
        return User::find($id);
    }
}
