<?php

use function Pest\Laravel\getJson;
use App\Models\Tweet;
use App\Models\User;

test('Deve retornar uma resposta paginada', function () {
    $user1 = User::factory()->create();
    Tweet::factory()->times(3)->fromUser($user1)->create();

    getJson(route('user.tweets', [$user1->username]))
        ->assertJsonStructure(['data', 'links', 'meta'])
        ->assertOk();
});

test('Deve retornar os tweets de um usuário específico', function () {
    $user1 = User::factory()->create();
    Tweet::factory()->times(3)->fromUser($user1)->create();
    $user2 = User::factory()->create();
    Tweet::factory()->times(4)->fromUser($user2)->create();

    $response = getJson(route('user.tweets', [$user2->username]))
        ->assertOk()
        ->getData();

    expect($response->data)->toBeArray();
    expect($response->data)->toHaveLength(4);
    foreach ($response->data as $tweet) {
        expect($tweet)->toHaveProperty('user_id', $user2->id);
    }
});

test('Deve permitir fazer paginação', function () {
    $user = User::factory()->create();
    $tweet1 = Tweet::factory()->fromUser($user)->create();
    $tweet2 = Tweet::factory()->fromUser($user)->create();

    $page = 1;
    $response1 = getJson(route('user.tweets', [$user->username, 'page' => $page, 'limit' => 1]))
        ->assertOk()
        ->getData();

    expect($response1->data)->toBeArray();
    expect($response1->data)->toHaveLength(1);
    expect($response1->data[0])->toMatchArray($tweet1->toArray());

    $page = 2;
    $response2 = getJson(route('user.tweets', [$user->username, 'page' => $page, 'limit' => 1]))
        ->assertOk()
        ->getData();

    expect($response2->data)->toBeArray();
    expect($response2->data)->toHaveLength(1);
    expect($response2->data[0])->toMatchArray($tweet2->toArray());
});
