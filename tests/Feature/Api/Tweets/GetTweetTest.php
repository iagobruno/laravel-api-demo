<?php

use function Pest\Laravel\{getJson, actingAs};

use App\Models\Tweet;
use App\Models\User;

test('Deve retornar um erro se o usuário não existir', function () {
    getJson(route('tweets.get', ['fakeusername', 'faketweetid']))
        ->assertNotFound()
        ->assertJsonFragment([
            'message' => 'User not found.'
        ]);
});

test('Deve retornar um erro se o tweet não existir', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    getJson(route('tweets.get', [$user->username, 'faketweetid']))
        ->assertNotFound()
        ->assertJsonFragment([
            'message' => 'Tweet not found.'
        ]);
});

test('Deve conseguir retornar as informações de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();

    getJson(route('tweets.get', [$user->username, $tweet->id]))
        ->assertOk()
        ->assertJson([
            'data' => $tweet->toArray()
        ]);
});

test('Deve retornar um erro se o tweet não for do usuário especificado na soliciação', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $tweetFromOtherUser = Tweet::factory()->for($otherUser)->create();

    getJson(route('tweets.get', [$user->username, $tweetFromOtherUser->id]))
        ->assertNotFound()
        ->assertJsonFragment([
            'message' => 'Tweet not found.'
        ]);
});
