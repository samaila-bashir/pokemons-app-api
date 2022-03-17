<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            "name" => ["required", "string"],
            "email" => ["required", "email", "unique:users"],
            "password" => ["required"]
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        $token = $user->createToken("apitoken")->plainTextToken;

        $reponse = [
            'user' => $user,
            'token' => $token
        ];

        return response($reponse, 201);
        
    }

    public function login(Request $request) {
        $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"]
        ]);

        // Confirm email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $token = $user->createToken("apitoken")->plainTextToken;

        $reponse = [
            'user' => $user,
            'token' => $token
        ];

        return response($reponse, 201);
        
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out.'
        ];
    }
}
