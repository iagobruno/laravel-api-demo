<?php

use function Pest\Laravel\{getJson, actingAs};

use App\Models\Tweet;
use App\Models\User;

test('Deve retornar um erro se o tweet não existir', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    getJson(route('tweet.show', ['faketweetid']))
        ->assertNotFound()
        ->assertJsonFragment([
            'message' => 'Tweet not found.'
        ]);
});

test('Deve conseguir retornar um tweet específico de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->for($user)->create();

    getJson(route('tweet.show', [$tweet->id]))
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
