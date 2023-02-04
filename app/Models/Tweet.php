<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jamesh\Uuid\HasUuid;

class Tweet extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'content',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [];

    /**
     * The event map for the model.
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\TweetCreated::class,
        'deleted' => \App\Events\TweetDeleted::class,
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
