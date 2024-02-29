<?php

use function Pest\Laravel\{actingAs, getJson};
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('Deve retornar um erro se o username não existir', function () {
    getJson(route('user.following', 'fakeusername'))
        ->assertNotFound();
});

test('Deve retornar uma resposta paginada', function () {
    $user = User::factory()->create();
    $otherUsers = User::factory(2)->create();
    $otherUsers->each(fn ($item) => $user->forceFollow($item));
    Sanctum::actingAs($user);

    getJson(route('user.following', $user))
        ->assertJsonStructure(['data', 'meta', 'links'])
        ->assertOk();
});

test('Deve retornar a lista de pessoas que o usuário segue', function () {
    $user = User::factory()->create();
    $otherUsers = User::factory(3)->create();
    $otherUsers->each(fn ($item) => $user->forceFollow($item));
    Sanctum::actingAs($user);

    getJson(route('user.following', $user))
        ->assertJson([
            'data' => $otherUsers->toArray()
        ])
        ->assertJsonCount(3, 'data');
});

test('Deve permitir fazer paginação', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUsers = User::factory(3)->create();
    $otherUsers->each(fn ($item) => $user->forceFollow($item));

    $page = 1;
    $response1 = actingAs($user, 'sanctum')
        ->getJson(route('user.following', [$user, 'page' => $page, 'per_page' => 1]))
        ->assertOk()
        ->getData();

    expect($response1->data)->toBeArray();
    expect($response1->data)->toHaveLength(1);
    expect($response1->data[0])->toMatchArray($otherUsers[0]->toArray());

    $page = 2;
    $response2 = actingAs($user, 'sanctum')
        ->getJson(route('user.following', [$user, 'page' => $page, 'per_page' => 1]))
        ->assertOk()
        ->getData();

    expect($response2->data)->toBeArray();
    expect($response2->data)->toHaveLength(1);
    expect($response2->data[0])->toMatchArray($otherUsers[1]->toArray());
});
