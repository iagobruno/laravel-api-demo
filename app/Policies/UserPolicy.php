<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $loggedUser, User $user): bool
    {
        return $user->is($loggedUser) && $loggedUser->tokenCan('profile:write');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $loggedUser, User $user): bool
    {
        return $user->is($loggedUser) && $loggedUser->tokenCan('profile:write');
    }

    /**
     * Determine whether the user can follow other user.
     */
    public function follow(User $loggedUser, User $otherUser): bool
    {
        return $loggedUser->tokenCan('followers:write');
    }

    /**
     * Determine whether the user can follow other user.
     */
    public function read_followers(User $loggedUser, User $otherUser): bool
    {
        return $loggedUser->tokenCan('followers:read');
    }
}
