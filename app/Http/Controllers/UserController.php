<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public static function index()
    {
        return User::all();
    }

    public static function store(Request $request)
    {
        $validated = $request->validate([
            'name' => "required|min:3|max:32",
            'email' => "required|email",
            'password' => "required|min:8"
        ]);
        return User::create($validated);
//        return $validated;
    }

    public static function update(Request $request)
    {
        $validated = $request->validate([
            'id' => "numeric",
            'name' => "min:3|max:32",
            'email' => "email",
            'password' => "min:8",
        ]);
        return (new User)->update($validated);
    }

    public static function destroy(int $id)
    {

        return (new User)->find($id)->delete();

    }

    public static function show(int $id)
    {
        return User::find($id);
    }
}
