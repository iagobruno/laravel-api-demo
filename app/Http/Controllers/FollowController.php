<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        auth()->user()->forceFollow($user);

        return response()->success('Successfully followed!');
    }

    public function unfollow(User $user)
    {
        auth()->user()->unfollow($user);

        return response()->success('Successfully unfollowed!');
    }
}
