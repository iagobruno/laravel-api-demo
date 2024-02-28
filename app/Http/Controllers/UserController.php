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
  *
  * APIs for managing users.
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
     * @apiResource App\Http\Resources\UserResource
     * @apiResourceModel App\Models\User
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
     * Get a user's tweets
     * @group Tweets
     *
     * @urlParam user_username string required The username of the user. Example: thay_26
     * @responseField meta object Contains information about the paginator's state.
     * @responseField links object Contains links to navigate.
     *
     * @apiResourceCollection App\Http\Resources\TweetResource
     * @apiResourceModel App\Models\Tweet paginate=2 with=user
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
