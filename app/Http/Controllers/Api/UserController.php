<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return response([
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($fields);

        return [
            'user' => $user,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            // 'email' => 'required|email|exists:users',
            // $user,
            
            'email' => 'required|email',Rule::unique('users')->ignore($user)
            // 'password' => 'required|confirmed'
        ]);

        if ($request->password) {
            $request->validate([
                'password' => 'required|confirmed'
            ]);
            $fields['password'] = bcrypt($request->password);
        }

        $user->update($fields);

        return [
            'user' => $user,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return [
            'message' => 'Data Deleted'
        ];
    }
}
