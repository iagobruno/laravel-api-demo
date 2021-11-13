<?php

use function Pest\Laravel\{getJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;

test('Deve retornar um erro se o username não existir', function () {
    getJson('/api/users/fakeusername')
        ->assertStatus(StatusCode::HTTP_NOT_FOUND);
});

test('Deve conseguir retornar as informações de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->getJson('/api/users/' . $user->username)
        ->assertExactJson($user->toArray())
        ->assertStatus(StatusCode::HTTP_OK);
});
