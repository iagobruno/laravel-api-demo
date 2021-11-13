<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

    public function updateMe(Request $request)
    {
        /** @var \App\Models\User */
        $loggedUser = auth()->user();
        Gate::authorize('update', $loggedUser);

        $data = $request->validate([
            'email' => ['sometimes', 'string', 'email', 'unique:users,email'],
            'username' => ['sometimes', 'string', 'min:4', 'max:16', 'regex:/^[0-9a-z\-\_]+$/i', 'unique:users,username'],
            'name' => ['sometimes', 'string', 'min:1', 'max:255'],
        ]);

        $loggedUser->fill($data);
        $loggedUser->save();

        return [
            'message' => 'Successfully updated!'
        ];
    }

    public function destroyMe()
    {
        /** @var \App\Models\User */
        $loggedUser = auth()->user();
        Gate::authorize('delete', $loggedUser);

        $loggedUser->delete();

        return [
            'message' => 'Successfully deleted!'
        ];
    }
}
