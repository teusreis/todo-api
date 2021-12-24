<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterUserRequest;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid email or password!'
            ]);
        }

        $token = $user->createToken('auth_token');

        return response()->json([
            'status' => 'ok',
            'message' => 'User registered successfully!',
            'token' => $token->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'ok',
            'message' => 'User logout'
        ]);
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        if (!$user) {
            return response()->json([
                'message' => 'The user could not be registered!'
            ], 500);
        }

        $token = $user->createToken('auth_token');

        return response()->json([
            'status' => 'ok',
            'message' => 'User registered successfully!',
            'token' => $token->plainTextToken
        ]);
    }
}
