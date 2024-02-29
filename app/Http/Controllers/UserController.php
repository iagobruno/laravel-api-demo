<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\PaginatedRequest;
use App\Http\Resources\{TweetCollection, UserResource, TweetResource};
use Illuminate\Support\Facades\Gate;
use F9Web\ApiResponseHelpers;
use App\Models\{Tweet, User};

/**
  * @group Users
  *
  * APIs for managing users.
  */
class UserController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Get a user
     *
     * Provides publicly available information about some user.
     *
     * @urlParam user_username string required The username of the user. Example: thay_26
     *
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * Get the authenticated user
     *
     * Returns the currently logged-in user's data.
     *
     * The token does not require any permissions.
     *
     * @authenticated
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
     */
    public function me()
    {
        return UserResource::make(auth()->user());
    }

    /**
     * Get a user's tweets
     * @group Tweets
     *
     * Returns a paginated response.
     *
     * @urlParam user_username string required The username of the user. Example: thay_26
     *
     * @apiResourceCollection App\Http\Resources\TweetCollection
     * @apiResourceModel App\Models\Tweet paginate=2 with=user
     */
    public function tweets(PaginatedRequest $request, User $user)
    {
        $tweets = Tweet::whereBelongsTo($user)
            ->with('user')
            ->latest()
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('per_page', 10)
            );

        return TweetCollection::make($tweets);
    }

    /**
     * Update the authenticated user
     *
     * Updates the currently logged-in user's data.
     *
     * The token must have the following permission: `profile:write`.
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
     * Delete the authenticated user
     *
     * Delete the currently logged-in user account.
     *
     * The token must have the following permission: `profile:write`.
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
