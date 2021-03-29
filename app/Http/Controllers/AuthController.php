<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $attr['name'],
            'password' => bcrypt($attr['password']),
            'email' => $attr['email']
        ]);

        return response()->json([
			'token' => $user->createToken('API Token')->plainTextToken
		], 201);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->input())) {
            return response()->json([
                'message' => 'Incorrect credentials'
            ], 401);
        }

        return response()->json([
            'id' => auth()->user()->id,
			'api_token' => auth()->user()->createToken('API Token')->plainTextToken
		], 200);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}