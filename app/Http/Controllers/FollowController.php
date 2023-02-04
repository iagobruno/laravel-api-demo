<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\{UserFollowed, UserUnfollowed};

class FollowController extends Controller
{
    public function follow(User $user)
    {
        auth()->user()->forceFollow($user);
        UserFollowed::dispatch($user, auth()->user());

        return response()->success('Successfully followed!');
    }

    public function unfollow(User $user)
    {
        auth()->user()->unfollow($user);
        UserUnfollowed::dispatch($user, auth()->user());

        return response()->success('Successfully unfollowed!');
    }
}
