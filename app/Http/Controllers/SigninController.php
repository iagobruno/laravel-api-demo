<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response as StatusCode;
use Illuminate\Support\Facades\Auth;

class SigninController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required_without:email', 'string'],
            'email' => ['required_without:username', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], StatusCode::HTTP_UNAUTHORIZED);
        }

        // Success!
        /** @var \App\Models\User */
        $user = Auth::user();
        $token = $user->createApiToken();

        return compact(['token', 'user']);
    }
}
