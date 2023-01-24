<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        /** @var \App\Models\User */
        $loggedUser = auth()->user();

        $loggedUser->forceFollow($user);

        Cache::forget(auth()->id() . '-following-ids');

        return [
            'message' => 'Successfully followed!'
        ];
    }

    public function unfollow(User $user)
    {
        /** @var \App\Models\User */
        $loggedUser = auth()->user();

        $loggedUser->unfollow($user);

        Cache::forget(auth()->id() . '-following-ids');

        return [
            'message' => 'Successfully unfollowed!'
        ];
    }
}
