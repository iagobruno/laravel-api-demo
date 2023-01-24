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

    public function view(User $user)
    {
        return $user->toArray();
    }

    public function updateMe(Request $request)
    {
        Gate::authorize('update-me');

        $data = $request->validate([
            'email' => ['sometimes', 'string', 'email', 'unique:users,email'],
            'username' => ['sometimes', 'string', 'min:4', 'max:16', 'regex:/^[0-9a-z\-\_]+$/i', 'unique:users,username'],
            'name' => ['sometimes', 'string', 'min:1', 'max:255'],
        ]);

        auth()->user()->update($data);

        return [
            'message' => 'Successfully updated!'
        ];
    }

    public function destroyMe()
    {
        Gate::authorize('delete-me');

        auth()->user()->delete();

        return [
            'message' => 'Successfully deleted!'
        ];
    }
}
