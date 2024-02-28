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

/**
  * @group Tweets
  */
class TweetController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Get a tweet
     *
     * Get a specific tweet by id.
     */
    public function show(Tweet $tweet)
    {
        return TweetResource::make($tweet->load('user'));
    }

    /**
     * Get user feed
     *
     * Get the latest tweets from profiles the user follows.
     *
     * @authenticated
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

    /**
     * Create a tweet
     *
     * Post a new tweet to the logged in user's account.
     *
     * @authenticated
     * @bodyParam content string The tweet content.
     * @response {
     *   id: 10022,
     *   content: "lorem ipsum dolor",
     *   created_at: "",
     *   updated_at: "",
     * }
    */
    public function store(StoreTweetRequest $request)
    {
        Gate::authorize('create', Tweet::class);

        $tweet = auth()->user()->tweets()->create($request->validated());

        return TweetResource::make($tweet);
    }

    /**
     * Delete a tweet
     *
     * @authenticated
    */
    public function destroy(Tweet $tweet)
    {
        Gate::authorize('delete', $tweet);

        $tweet->delete();

        return $this->respondOK('Successfully deleted!');
    }
}
