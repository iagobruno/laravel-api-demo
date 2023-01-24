<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Skybluesofa\Followers\Traits\Followable;
use Jamesh\Uuid\HasUuid;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use Followable;
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'viewer_follows',
        'followers_count',
        'following_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function tweets()
    {
        return $this->hasMany(Tweet::class);
    }


    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function createApiToken()
    {
        return $this->createToken(request()->device_name ?? 'api')->plainTextToken;
    }

    public function forceFollow(User $userToFollow)
    {
        $this->follow($userToFollow);
        $userToFollow->acceptFollowRequestFrom($this);
    }

    public function getViewerFollowsAttribute()
    {
        return auth()->user()?->isFollowing($this) ?? false;
    }

    public function getFollowersCountAttribute()
    {
        return $this->getFollowedByCount();
    }

    public function getFollowingCountAttribute()
    {
        return $this->getFollowingCount();
    }
}
