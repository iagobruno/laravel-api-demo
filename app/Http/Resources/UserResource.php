<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @responseField viewer_follows boolean Indicates if the logged-in user follows the requested user.
     * @responseField followers_count integer Number of followers.
     * @responseField following_count integer Number of accounts the user follows.
     * @responseField tweets_url string URI to fetch user's tweets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource->attributesToArray() + [
            'viewer_follows' => auth()->user()?->isFollowing($this->resource) ?? false,
            'followers_count' => $this->getFollowedByCount(),
            'following_count' => $this->getFollowingCount(),
            'tweets_url' => route('user.tweets', $this->resource->username),
        ];
    }
}
