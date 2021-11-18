<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FollowController extends Controller
{
    public function follow($username)
    {
        $userToFollow = User::findByUsernameOrFail($username);
        /** @var \App\Models\User */
        $loggedUser = auth()->user();

        $loggedUser->forceFollow($userToFollow);

        Cache::forget(auth()->id() . '-following-ids');

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

        Cache::forget(auth()->id() . '-following-ids');

        return [
            'message' => 'Successfully unfollowed!'
        ];
    }
}
