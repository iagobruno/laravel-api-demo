<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\PaginatedRequest;
use App\Http\Resources\{UserResource, TweetResource};
use Illuminate\Support\Facades\Gate;
use F9Web\ApiResponseHelpers;
use App\Models\{Tweet, User};

class UserController extends Controller
{
    use ApiResponseHelpers;

    public function me()
    {
        return UserResource::make(auth()->user());
    }

    public function show(User $user)
    {
        return UserResource::make($user);
    }

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

    public function update(UpdateUserRequest $request)
    {
        Gate::authorize('update', $user = auth()->user());

        $user->update($request->validated());

        return $this->respondOk('Successfully updated!');
    }

    public function destroy()
    {
        Gate::authorize('delete', $user = auth()->user());

        $user->delete();

        return $this->respondOk('Successfully deleted!');
    }
}
