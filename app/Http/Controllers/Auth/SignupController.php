<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SignupRequest;
use F9Web\ApiResponseHelpers;
use App\Models\User;

class SignupController extends Controller
{
    use ApiResponseHelpers;

    public function __invoke(SignupRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);
        $token = $user->createApiToken();

        return $this->respondWithSuccess([
            'token' => $token,
        ]);
    }
}
