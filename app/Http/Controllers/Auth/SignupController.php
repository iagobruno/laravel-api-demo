<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SignupRequest;
use App\Models\User;

class SignupController extends Controller
{
    public function __invoke(SignupRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);
        $token = $user->createApiToken();

        return response()->success('Account created successfully!', [
            'token' => $token,
        ]);
    }
}
