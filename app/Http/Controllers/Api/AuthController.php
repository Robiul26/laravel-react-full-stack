<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);
        // $user = User::create($fields);
        // $token = $user->createToken($request->name);
        if (!Auth::attempt($fields)) {
            return response([
                'message' => 'Provided email or password is incorrect.'
            ], 422);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return response([
            'user' => $user,
            'token' => $token
        ]);

        // return  [
        //     'user' => $user,
        //     'token' => $token->plainTextToken,
        // ];
    }



    public function signup(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);
        $user = User::create($fields);
        $token = $user->createToken($request->name);

        return  [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
    }
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response('', 204);
        // $request->user()->tokens()->delete();

        // return ['message' => 'Logged out'];
    }
}
