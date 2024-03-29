<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PaginatedRequest;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Resources\{TweetResource, TweetCollection};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use F9Web\ApiResponseHelpers;
use App\Models\{Tweet, User};

/**
  * @group Tweets
  *
  * APIs for managing tweets.
  */
class TweetController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Get a tweet
     *
     * Get a specific tweet by id.
     *
     * @urlParam tweet_id string required The ID of the tweet. Example: 01hqvyv4xakkdn5wb41hzknsg9
     * @responseField user object Contains the infos of the tweet author.
     *
     * @apiResource App\Http\Resources\TweetResource
     * @apiResourceModel App\Models\Tweet with=user
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
     * Returns a paginated response.
     *
     * The token does not require any permissions.
     *
     * @authenticated
     *
     * @apiResourceCollection App\Http\Resources\TweetCollection
     * @apiResourceModel App\Models\Tweet paginate=2 with=user
     */
    public function feed(PaginatedRequest $request)
    {
        $tweets = Tweet::query()
            ->whereIn('user_id', auth()->user()->following()->select('recipient_id'))
            ->orWhereBelongsTo(auth()->user())
            ->with('user')
            ->latest()
            // ->dd() // Debug the final sql query
            ->paginate(
                page: $request->input('page', 1),
                perPage: $request->input('per_page', 10)
            );

        return TweetCollection::make($tweets);
    }

    /**
     * Create a tweet
     *
     * Post a new tweet to the logged in user's account.
     *
     * The token must have the following permission: `tweet:write`.
     *
     * @authenticated
     * @bodyParam content string required The tweet content. Max 140 caracteres. Example: Lorem ipsum dolor sit...
     *
     * @apiResource App\Http\Resources\TweetResource
     * @apiResourceModel App\Models\Tweet with=user
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
     * The token must have the following permission: `tweet:write`.
     *
     * @authenticated
     * @urlParam tweet_id string required The ID of the tweet. Example: 01hqvyv4xakkdn5wb41hzknsg9
    */
    public function destroy(Tweet $tweet)
    {
        Gate::authorize('delete', $tweet);

        $tweet->delete();

        return $this->respondOK('Successfully deleted!');
    }
}
