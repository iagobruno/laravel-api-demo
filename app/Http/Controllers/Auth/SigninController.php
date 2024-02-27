<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SigninRequest;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth;
use F9Web\ApiResponseHelpers;
use App\Events\Signin;

class SigninController extends Controller
{
    use ApiResponseHelpers;

    public function __invoke(SigninRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            throw new InvalidCredentialsException(__('auth.failed'));
        }

        /** @var \App\Models\User */
        $user = Auth::user();
        $token = $user->createApiToken();
        Signin::dispatch($user);

        return $this->respondWithSuccess(compact(['token', 'user']));
    }
}
