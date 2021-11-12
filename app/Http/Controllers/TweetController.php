<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TweetController extends Controller
{
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
