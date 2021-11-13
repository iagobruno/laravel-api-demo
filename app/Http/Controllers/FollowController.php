<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow($username)
    {
        $userToFollow = User::findByUsernameOrFail($username);
        /** @var \App\Models\User */
        $loggedUser = auth()->user();

        $loggedUser->follow($userToFollow);
        $userToFollow->acceptFollowRequestFrom($loggedUser);

        return [
            'message' => ''
        ];
    }
}
