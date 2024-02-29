<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{getJson};

test('Deve retornar um erro se a solicitação não houver um token', function () {
    getJson(route('user.follows', 'fakeuser'))
        ->assertUnauthorized();
});

test('O token de acesso deve ter a permissão necessária', function () {
    [$user1, $user2] = User::factory(2)->create();
    $user1->forceFollow($user2);
    Sanctum::actingAs($user1);

    getJson(route('user.follows', [$user2]))
        ->assertJson([
            'message' => 'You don\'t have permission to perform this action.'
        ])
        ->assertForbidden();
});

test('Indica se o usuário autenticado segue ou não outro usuário', function () {
    [$user1, $user2, $user3] = User::factory(3)->create();
    $user1->forceFollow($user2);
    Sanctum::actingAs($user1, ['followers:read']);

    getJson(route('user.follows', [$user2]))
        ->assertOk();

    getJson(route('user.follows', [$user3]))
        ->assertNotFound();
});
