<?php

use function Pest\Laravel\{getJson, actingAs};
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    getJson(route('me.show'))
        ->assertUnauthorized();
});

test('Deve retornar as informações do usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->getJson(route('me.show'))
        ->assertJson([
            'data' => $user->toArray(),
        ])
        ->assertOk();
});
