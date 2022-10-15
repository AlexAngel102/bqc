<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => "required|min:3|max:32",
            'email' => "required|email",
            'password' => "required|min:8"
        ]);
        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validated->errors()
            ], 401);
        }
        $request['password'] = Hash::make($request['password']);
        try {
            User::create($request->only('name', 'email', 'password'));
        } catch (\Exception $exception) {
            error_log($exception);
            return response()->json(['error' => 'User already exists'], 403);
        }
        return response()->json("User created", 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = Validator::make($request->all(),[
            'name' => "min:3|max:32",
            'email' => "email",
            'password' => "min:8",
        ]);
        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validated->errors()
            ], 401);
        }
        $request['password'] = Hash::make($request->password);
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
