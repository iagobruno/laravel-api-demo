<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\{UserFollowed, UserUnfollowed};
use App\Http\Requests\PaginatedRequest;
use App\Http\Resources\FollowersCollection;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Facades\Gate;

/**
  * @group Users
  * @subgroup Followers
  */
class FollowController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Check if the authenticated user follows a user
     *
     * Responds with 200 status code if the authenticated user follows the specified user and 404 if not.
     *
     * The token must have the following permission: `followers:read`.
     *
     * @authenticated
     * @urlParam user_username string required The username of the user. Example: thay_26
     * @response status=200 scenario="if the person is followed by the authenticated user" true
     * @response status=404 scenario="if the person is not followed by the authenticated user" false
     */
    public function follows(User $user)
    {
        Gate::authorize('read_followers', $user);

        if (auth()->user()->isFollowing($user)) {
            return $this->respondOk('true');
        } else {
            return $this->respondNotFound('false');
        }
    }

    /**
     * List followers of a user
     *
     * Lists the people following the specified user.
     *
     * Returns a paginated response.
     *
     * @urlParam user_username string required The username of the user. Example: thay_26
     *
     * @apiResourceCollection App\Http\Resources\FollowersCollection
     * @apiResourceModel App\Models\User paginate=2
     */
    public function followers(PaginatedRequest $request, User $user)
    {
        $list = User::query()
            ->whereIn('id', auth()->user()->followers()->select('sender_id'))
            // ->dd() // Debug the final sql query
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('per_page', 10)
            );

        return FollowersCollection::make($list);
    }

    /**
     * List the people a user follows
     *
     * Lists the people who the specified user follows.
     *
     * Returns a paginated response.
     *
     * @urlParam user_username string required The username of the user. Example: thay_26
     *
     * @apiResourceCollection App\Http\Resources\FollowersCollection
     * @apiResourceModel App\Models\User paginate=2
     */
    public function following(PaginatedRequest $request, User $user)
    {
        $list = User::query()
            ->whereIn('id', auth()->user()->following()->select('recipient_id'))
            // ->dd() // Debug the final sql query
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('per_page', 10)
            );

        return FollowersCollection::make($list);
    }

    /**
     * Follow another user
     *
     * Makes the logged in user follow another user.
     *
     * The token must have the following permission: `followers:write`.
     *
     * @authenticated
     * @urlParam user_username string required The username of the user to follow. Example: thay_26
     */
    public function follow(User $user)
    {
        Gate::authorize('follow', $user);

        auth()->user()->forceFollow($user);
        UserFollowed::dispatch($user, auth()->user());

        return $this->respondOk('Successfully followed!');
    }

    /**
     * Unfollow a user
     *
     * Makes the logged in user unfollow a user.
     *
     * The token must have the following permission: `followers:write`.
     *
     * @authenticated
     * @urlParam user_username string required The username of the user to unfollow. Example: thay_26
     */
    public function unfollow(User $user)
    {
        Gate::authorize('follow', $user);

        auth()->user()->unfollow($user);
        UserUnfollowed::dispatch($user, auth()->user());

        return $this->respondOk('Successfully unfollowed!');
    }
}
