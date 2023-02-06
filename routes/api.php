<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Auth\SigninController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;
use App\Exceptions\RouteNotFoundException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/signup', SignupController::class)->name('signup');
Route::post('/signin', SigninController::class)->name('signin');

Route::get('/users/{user}', [UserController::class, 'view'])->name('user.get');
Route::get('/users/{user}/tweets', [TweetController::class, 'userTweets'])->name('tweets.from_user');
Route::get('/tweets/{tweet}', [TweetController::class, 'show'])->name('tweet.get');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/feed', [TweetController::class, 'feed'])->name('feed');

    Route::post('/tweets', [TweetController::class, 'store'])->name('tweet.store');
    Route::delete('/tweets/{tweet}', [TweetController::class, 'destroy'])->name('tweet.delete');

    Route::get('/me', [UserController::class, 'me'])->name('user.me');
    Route::patch('/me', [UserController::class, 'updateMe'])->name('user.update');
    Route::delete('/me', [UserController::class, 'destroyMe'])->name('user.destroy');

    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::post('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('user.unfollow');
});

Route::fallback(function () {
    throw new RouteNotFoundException;
});
