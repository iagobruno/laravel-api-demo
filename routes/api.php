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

Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
Route::get('/users/{user}/tweets', [UserController::class, 'tweets'])->name('user.tweets');
Route::get('/users/{user}/followers', [FollowController::class, 'followers'])->name('user.followers');
Route::get('/users/{user}/following', [FollowController::class, 'following'])->name('user.following');

Route::get('/tweets/{tweet}', [TweetController::class, 'show'])->name('tweet.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/feed', [TweetController::class, 'feed'])->name('feed');

    Route::post('/tweets', [TweetController::class, 'store'])->name('tweet.store');
    Route::delete('/tweets/{tweet}', [TweetController::class, 'destroy'])->name('tweet.destroy');

    Route::controller(UserController::class)->name('me.')->group(function () {
        Route::get('/me', 'me')->name('show');
        Route::patch('/me', 'update')->name('update');
        Route::delete('/me', 'destroy')->name('destroy');
    });

    Route::controller(FollowController::class)->name('user.')->group(function () {
        Route::get('/me/following/{user}', 'follows')->name('follows');
        Route::put('/me/following/{user}', 'follow')->name('follow');
        Route::delete('/me/following/{user}', 'unfollow')->name('unfollow');
    });
});

/**
 * @hideFromAPIDocumentation
 */
Route::fallback(function () {
    throw new RouteNotFoundException;
});
