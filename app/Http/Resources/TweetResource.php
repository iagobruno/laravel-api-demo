<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TweetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource->attributesToArray() + [
            'user' => UserResource::make($this->whenLoaded('user')),
            $this->mergeWhen($this->relationLoaded('user'), [
                'user_url' => route('user.get', $this->user->username),
            ]),
        ];
    }
}
