<?php

use function Pest\Laravel\getJson;
use Illuminate\Http\Response as StatusCode;
use App\Models\Tweet;
use App\Models\User;

test('Deve retornar uma resposta paginada', function () {
    $user1 = User::factory()->create();
    Tweet::factory()->times(3)->create(['user_id' => $user1->id]);

    getJson("/api/users/{$user1->username}/tweets")
        ->assertJsonStructure(['data', 'current_page', 'next_page_url'])
        ->assertStatus(StatusCode::HTTP_OK);
});

test('Deve retornar os tweets de um usuário específico', function () {
    $user1 = User::factory()->create();
    Tweet::factory()->times(3)->create(['user_id' => $user1->id]);
    $user2 = User::factory()->create();
    Tweet::factory()->times(4)->create(['user_id' => $user2->id]);

    $response = getJson("/api/users/{$user2->username}/tweets")
        ->assertStatus(StatusCode::HTTP_OK)
        ->getData();

    expect($response->data)->toBeArray();
    expect($response->data)->toHaveLength(4);
    expect($response->data)->each(
        fn ($tweet) => $tweet->toHaveProperty('user_id', $user2->id)
    );
});

test('Deve permitir fazer paginação', function () {
    $user = User::factory()->create();
    $tweet1 = Tweet::factory()->create(['user_id' => $user->id]);
    $tweet2 = Tweet::factory()->create(['user_id' => $user->id]);

    $page = 1;
    $response1 = getJson("/api/users/{$user->username}/tweets?page=$page&perPage=1")
        ->assertStatus(StatusCode::HTTP_OK)
        ->getData();

    expect($response1->data)->toBeArray();
    expect($response1->data)->toHaveLength(1);
    expect($response1->data[0])->toMatchArray($tweet1->toArray());

    $page = 2;
    $response2 = getJson("/api/users/{$user->username}/tweets?page=$page&perPage=1")
        ->assertStatus(StatusCode::HTTP_OK)
        ->getData();

    expect($response2->data)->toBeArray();
    expect($response2->data)->toHaveLength(1);
    expect($response2->data[0])->toMatchArray($tweet2->toArray());
});
