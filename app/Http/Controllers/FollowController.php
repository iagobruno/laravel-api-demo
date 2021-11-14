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

        $loggedUser->forceFollow($userToFollow);

        return [
            'message' => 'Successfully followed!'
        ];
    }

    public function unfollow($username)
    {
        $userToUnfollow = User::findByUsernameOrFail($username);
        /** @var \App\Models\User */
        $loggedUser = auth()->user();

        $loggedUser->unfollow($userToUnfollow);

        return [
            'message' => 'Successfully unfollowed!'
        ];
    }
}
