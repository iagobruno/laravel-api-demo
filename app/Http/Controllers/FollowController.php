<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\{UserFollowed, UserUnfollowed};
use F9Web\ApiResponseHelpers;

/**
  * @group Users
  */
class FollowController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Follow another user
     *
     * Makes the logged in user follow another user.
     *
     * @urlParam user_username string required The username of the user to follow. Example: thay_26
     */
    public function follow(User $user)
    {
        auth()->user()->forceFollow($user);
        UserFollowed::dispatch($user, auth()->user());

        return $this->respondOk('Successfully followed!');
    }

    /**
     * Unfollow a user
     *
     * Makes the logged in user unfollow a user.
     *
     * @urlParam user_username string required The username of the user to unfollow. Example: thay_26
     */
    public function unfollow(User $user)
    {
        auth()->user()->unfollow($user);
        UserUnfollowed::dispatch($user, auth()->user());

        return $this->respondOk('Successfully unfollowed!');
    }
}
