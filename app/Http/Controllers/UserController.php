<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\PaginatedRequest;
use App\Http\Resources\{UserResource, TweetResource};
use Illuminate\Support\Facades\Gate;
use F9Web\ApiResponseHelpers;
use App\Models\{Tweet, User};

/**
  * @group Users
  */
class UserController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Get the logged in user
     *
     * Returns the currently logged-in user's data.
     *
     * @authenticated
     * @response {
     *    "id": 4,
     *    "name": "Jessica Jones",
     *    "username": "jessica_jones",
     *    "email": "jessica@gmail.com",
     *    "viewer_follows": true,
     *    "followers_count": 10,
     *    "following_count": 100,
     *    "tweets_url": "..."
     * }
     */
    public function me()
    {
        return UserResource::make(auth()->user());
    }

    /**
     * Get a user
     *
     * Returns the user data.
     *
     * @urlParam user_username string required The username of the user.
     * @response {
     *    "id": 4,
     *    "name": "Jessica Jones",
     *    "username": "jessica_jones",
     *    "email": "jessica@gmail.com",
     *    "viewer_follows": true,
     *    "followers_count": 10,
     *    "following_count": 100,
     *    "tweets_url": "..."
     * }
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * Get a user's tweets
     *
     * @urlParam user_username string required The username of the user.
     * @bodyParam page integer The page number of the results to fetch.
     * @bodyParam limit integer The number of results per page to be returned. Max 50 and the default is 10.
     * @response {
     *   "data": [
     *     { ... },
     *     { ... },
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 100,
     *     "per_page": 10,
     *     ...
     *   },
     *   "links": {...},
     * }
     */
    public function tweets(PaginatedRequest $request, User $user)
    {
        $tweets = Tweet::whereBelongsTo($user)
            ->latest()
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('limit', 10)
            );

        return TweetResource::collection($tweets);
    }

    /**
     * Update user data
     *
     * Updates the currently logged-in user's data.
     *
     * @authenticated
     */
    public function update(UpdateUserRequest $request)
    {
        Gate::authorize('update', $user = auth()->user());

        $user->update($request->validated());

        return $this->respondOk('Successfully updated!');
    }

    /**
     * Delete user account
     *
     * Delete the currently logged-in user account.
     *
     * @authenticated
     */
    public function destroy()
    {
        Gate::authorize('delete', $user = auth()->user());

        $user->delete();

        return $this->respondOk('Successfully deleted!');
    }
}
