<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TweetCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @responseField meta object Contains information about the paginator's state.
     * @responseField links object Contains links to navigate.
     */
    public function toArray(Request $request)
    {
        return TweetResource::collection($this->collection);
        // return parent::toArray($request);
    }
}
