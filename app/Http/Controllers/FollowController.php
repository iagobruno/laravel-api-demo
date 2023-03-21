<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\{UserFollowed, UserUnfollowed};
use F9Web\ApiResponseHelpers;

class FollowController extends Controller
{
    use ApiResponseHelpers;

    public function follow(User $user)
    {
        auth()->user()->forceFollow($user);
        UserFollowed::dispatch($user, auth()->user());

        return $this->respondOk('Successfully followed!');
    }

    public function unfollow(User $user)
    {
        auth()->user()->unfollow($user);
        UserUnfollowed::dispatch($user, auth()->user());

        return $this->respondOk('Successfully unfollowed!');
    }
}
