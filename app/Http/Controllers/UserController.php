<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class UserController extends Controller
{
    public function me()
    {
        return UserResource::make(auth()->user());
    }

    public function view(User $user)
    {
        return UserResource::make($user);
    }

    public function updateMe(UpdateUserRequest $request)
    {
        Gate::authorize('update-me');

        $data = $request->validated();
        auth()->user()->update($data);

        return response()->success('Successfully updated!');
    }

    public function destroyMe()
    {
        Gate::authorize('delete-me');

        auth()->user()->delete();

        return response()->success('Successfully deleted!');
    }
}
