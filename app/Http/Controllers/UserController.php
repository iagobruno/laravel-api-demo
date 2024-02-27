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

    public function view(User $user)
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

    public function updateMe(UpdateUserRequest $request)
    {
        Gate::authorize('update-me');

        $data = $request->validated();
        auth()->user()->update($data);

        return $this->respondOk('Successfully updated!');
    }

    public function destroyMe()
    {
        Gate::authorize('delete-me');

        auth()->user()->delete();

        return $this->respondOk('Successfully deleted!');
    }
}
