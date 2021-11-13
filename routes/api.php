<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;

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

Route::post('/signup', SignupController::class);
Route::post('/signin', SigninController::class);

Route::get('/users/{username}', [UserController::class, 'view']);
Route::get('/users/{username}/tweets', [TweetController::class, 'tweetsFromUser']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tweets', [TweetController::class, 'store']);
    Route::delete('/tweets/{tweet}', [TweetController::class, 'destroy']);

    Route::get('/me', [UserController::class, 'me']);
    Route::patch('/me', [UserController::class, 'updateMe']);
    Route::delete('/me', [UserController::class, 'destroyMe']);

    Route::post('/users/{username}/follow', [FollowController::class, 'follow']);
});
