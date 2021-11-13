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

    /**
     * @return \App\Models\User
     */
    static function findByUsernameOrFail(string $username)
    {
        return User::where('username', $username)->firstOrFail();
    }
}
