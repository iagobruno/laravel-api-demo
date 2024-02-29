<?php

use function Pest\Laravel\{actingAs, getJson};
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('Deve retornar um erro se o username não existir', function () {
    getJson(route('user.followers', 'fakeusername'))
        ->assertNotFound();
});

test('Deve retornar uma resposta paginada', function () {
    $user = User::factory()->create();
    $users = User::factory(2)->create();
    $users->each->forceFollow($user);
    Sanctum::actingAs($user);

    getJson(route('user.followers', $user))
        ->assertJsonStructure(['data', 'meta', 'links'])
        ->assertOk();
});

test('Deve retornar a lista de seguidores', function () {
    $user = User::factory()->create();
    $users = User::factory(3)->create();
    $users->each->forceFollow($user);
    Sanctum::actingAs($user);

    getJson(route('user.followers', $user))
        ->assertJson([
            'data' => $users->toArray()
        ])
        ->assertJsonCount(3, 'data');
});

test('Deve permitir fazer paginação', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUsers = User::factory(3)->create();
    $otherUsers->each->forceFollow($user);

    $page = 1;
    $response1 = actingAs($user, 'sanctum')
        ->getJson(route('user.followers', [$user, 'page' => $page, 'per_page' => 1]))
        ->assertOk()
        ->getData();

    expect($response1->data)->toBeArray();
    expect($response1->data)->toHaveLength(1);
    expect($response1->data[0])->toMatchArray($otherUsers[0]->toArray());

    $page = 2;
    $response2 = actingAs($user, 'sanctum')
        ->getJson(route('user.followers', [$user, 'page' => $page, 'per_page' => 1]))
        ->assertOk()
        ->getData();

    expect($response2->data)->toBeArray();
    expect($response2->data)->toHaveLength(1);
    expect($response2->data[0])->toMatchArray($otherUsers[1]->toArray());
});
