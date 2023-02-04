<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth;
use App\Events\Signin;

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
            throw new InvalidCredentialsException(__('auth.failed'));
        }

        // Success!
        /** @var \App\Models\User */
        $user = Auth::user();
        $token = $user->createApiToken();
        Signin::dispatch($user);

        return response()->success('Welcome back!', compact(['token', 'user']));
    }
}
