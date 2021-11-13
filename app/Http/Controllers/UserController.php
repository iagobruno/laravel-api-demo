<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me()
    {
        return auth()->user();
    }

    public function view($username)
    {
        return User::firstWhere('username', $username);
    }
}
