<?php

use function Pest\Laravel\{getJson, actingAs};

use App\Models\User;
use Illuminate\Http\Response as StatusCode;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    getJson(route('user.me'))
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar as informações do usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->getJson(route('user.me'))
        ->assertExactJson($user->toArray())
        ->assertStatus(StatusCode::HTTP_OK);
});
