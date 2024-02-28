<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Skybluesofa\Followers\Traits\Followable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasUuids;
    use HasApiTokens, HasFactory, Notifiable;
    use Followable;

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
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The event map for the model.
     */
    protected $dispatchesEvents = [
        'created' => \App\Events\UserCreated::class,
        'updated' => \App\Events\UserUpdated::class,
        'deleted' => \App\Events\UserDeleted::class,
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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }
}
