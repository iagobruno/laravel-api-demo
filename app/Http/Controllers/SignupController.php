<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'min:4', 'max:16', 'unique:users,username'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $user = User::create($data);
        $token = $user->createApiToken();

        return [
            'message' => 'OK',
            'token' => $token,
        ];
    }
}
