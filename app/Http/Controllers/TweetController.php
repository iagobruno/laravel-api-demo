<?php

namespace App\Http\Controllers;

use App\Models\{Tweet, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TweetController extends Controller
{
    public function feed()
    {
        $page = request('page', 1);
        $perPage = request('perPage', 10);

        $followingSubQuery = auth()->user()->following()
            ->select('recipient_id');

        return Tweet::query()
            ->whereIn('user_id', $followingSubQuery)
            ->orWhere('user_id', auth()->id())
            ->latest()
            ->with('user')
            // ->dd() // Debug the final sql query
            ->paginate(page: $page, perPage: $perPage);
    }

    public function tweetsFromUser(User $user)
    {
        $page = request('page', 1);
        $perPage = request('perPage', 10);

        return Tweet::query()
            ->whereBelongsTo($user)
            ->latest()
            ->paginate(page: $page, perPage: $perPage);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Tweet::class);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:140']
        ]);

        /** @var \App\Models\User */
        $user = auth()->user();
        $tweet = $user->tweets()->create($data);

        return $tweet;
    }

    public function destroy(Tweet $tweet)
    {
        Gate::authorize('delete', $tweet);

        $tweet->delete();

        return [
            'message' => 'Successfully deleted!'
        ];
    }
}
