<?php

use function Pest\Laravel\{getJson, actingAs};
use App\Models\User;

test('Deve retornar um erro se o username não existir', function () {
    getJson(route('user.show', ['fakeusername']))
        ->assertNotFound();
});

test('Deve conseguir retornar as informações de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    getJson(route('user.show', [$user->username]))
        ->assertJson([
            'data' => $user->toArray()
        ])
        ->assertOk();
});

test('Deve informar se o usuário logado segue o usuário solicitado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $user->forceFollow($otherUser);

    actingAs($user, 'sanctum')
        ->getJson(route('user.show', [$otherUser->username]))
        ->assertJson([
            'data' => [
                'viewer_follows' => true,
            ]
        ])
        ->assertOk();
});

test('Deve informar o número de seguidores', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUsers = User::factory(2)->create();
    $otherUsers->each->forceFollow($user);

    getJson(route('user.show', [$user->username]))
        ->assertJson([
            'data' => [
                'followers_count' => 2
            ]
        ])
        ->assertOk();
});

test('Deve informar o número de pessoas que o usuário segue', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUsers = User::factory(3)->create();
    $otherUsers->each(fn ($otherUser) => $user->forceFollow($otherUser));

    getJson(route('user.show', [$user->username]))
        ->assertJson([
            'data' => [
                'following_count' => 3
            ]
        ])
        ->assertOk();
});
