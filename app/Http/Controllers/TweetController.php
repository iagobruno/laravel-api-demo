<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TweetController extends Controller
{
    public function feed()
    {
        $page = request('page', 1);
        $perPage = request('perPage', 10);

        $following = Cache::remember(auth()->id() . '-following-ids', now()->addHours(1), function () {
            # Get the list of people the logged in user follows
            return DB::table(config('followers.tables.followers'))
                ->select('recipient_id')
                ->where('sender_id', auth()->id())
                ->get()
                ->pluck('recipient_id')
                ->toArray();
        });

        return Tweet::query()
            ->whereIn('user_id', $following)
            ->orWhere('user_id', auth()->id())
            ->latest()
            ->with('user')
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
