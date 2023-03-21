<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Resources\TweetResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use F9Web\ApiResponseHelpers;
use App\Models\{Tweet, User};

class TweetController extends Controller
{
    use ApiResponseHelpers;

    public function show(Tweet $tweet)
    {
        $tweet->load('user');
        return TweetResource::make($tweet);
    }

    public function feed(Request $request)
    {
        $followingSubQuery = auth()->user()->following()
            ->select('recipient_id');
        $tweets = Tweet::query()
            ->whereIn('user_id', $followingSubQuery)
            ->orWhere('user_id', auth()->id())
            ->latest()
            ->with('user')
            // ->dd() // Debug the final sql query
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('limit', 10)
            );

        return TweetResource::collection($tweets);
    }

    public function userTweets(Request $request, User $user)
    {
        $tweets = Tweet::query()
            ->whereBelongsTo($user)
            ->latest()
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('limit', 10)
            );

        return TweetResource::collection($tweets);
    }

    public function store(StoreTweetRequest $request)
    {
        Gate::authorize('create', Tweet::class);

        $data = $request->validated();
        /** @var \App\Models\User */
        $user = auth()->user();

        $tweet = $user->tweets()->create($data);

        return TweetResource::make($tweet);
    }

    public function destroy(Tweet $tweet)
    {
        Gate::authorize('delete', $tweet);

        $tweet->delete();

        return $this->respondOK('Successfully deleted!');
    }
}
