<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        auth()->user()->forceFollow($user);

        return [
            'message' => 'Successfully followed!'
        ];
    }

    public function unfollow(User $user)
    {
        auth()->user()->unfollow($user);

        return [
            'message' => 'Successfully unfollowed!'
        ];
    }
}
