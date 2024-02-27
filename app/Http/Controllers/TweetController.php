<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PaginatedRequest;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Resources\TweetResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use F9Web\ApiResponseHelpers;
use App\Models\{Tweet, User};

class TweetController extends Controller
{
    use ApiResponseHelpers;

    public function show(User $user, Tweet $tweet)
    {
        return TweetResource::make($tweet->load('user'));
    }

    public function feed(PaginatedRequest $request)
    {
        $tweets = Tweet::query()
            ->whereIn('user_id', auth()->user()->following()->select('recipient_id'))
            ->orWhereBelongsTo(auth()->user())
            ->latest()
            ->with('user')
            // ->dd() // Debug the final sql query
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('limit', 10)
            );

        return TweetResource::collection($tweets);
    }

    public function store(StoreTweetRequest $request)
    {
        Gate::authorize('create', Tweet::class);

        $tweet = auth()->user()->tweets()->create($request->validated());

        return TweetResource::make($tweet);
    }

    public function destroy(Tweet $tweet)
    {
        Gate::authorize('delete', $tweet);

        $tweet->delete();

        return $this->respondOK('Successfully deleted!');
    }
}
