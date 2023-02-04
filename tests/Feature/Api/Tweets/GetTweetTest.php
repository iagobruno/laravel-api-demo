<?php

use function Pest\Laravel\{getJson, actingAs};

use App\Models\Tweet;
use App\Models\User;

test('Deve retornar um erro se o tweet não existir', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    getJson(route('tweet.get', 'faketweetid'))
        ->assertNotFound()
        ->assertJsonFragment([
            'message' => 'Tweet not found.'
        ]);
});

test('Deve conseguir retornar um tweet', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();

    getJson(route('tweet.get', $tweet->id))
        ->assertOk()
        ->assertJson([
            'data' => $tweet->toArray()
        ])
        ->assertJson([
            'data' => [
                'user' => $user->toArray()
            ]
        ]);
});
