<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;

class ApiAuthController extends Controller
{
    use HasAttributes;

    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function registerUser(Request $request)
    {
        try {
            if (!$this->loginUser($request)->original['status']) {
                $user = User::where('email', $request->email)->first();
                /**/
                $validateUser = Validator::make($request->all(),
                    [
                        'name' => 'required',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required'
                    ]);

                if ($validateUser->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'validation error',
                        'errors' => $validateUser->errors()
                    ], 401);
                }

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'API User registred successfully',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'User already exists. Try to login!'
            ], 200);

        } catch (\Throwable $exeption) {
            return response()->json([
                'status' => false,
                'message' => $exeption->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken($user->email)->plainTextToken
            ], 200);

        } catch (\Throwable $exeption) {
            return response()->json([
                'status' => false,
                'message' => $exeption->getMessage()
            ], 500);
        }
    }

    public function logoutUser(Request $request)
    {
        try {

            $user = User::where('email', $request->email)->first();
            $user->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'User Logged Out Successfully'
            ], 500);

        } catch (\Throwable $exeption) {
            return response()->json([
                'status' => false,
                'message' => $exeption->getMessage()
            ], 500);
        }
    }
}
