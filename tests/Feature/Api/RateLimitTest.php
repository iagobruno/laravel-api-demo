<?php

use function Pest\Laravel\{getJson, actingAs};
use App\Models\User;
use Illuminate\Http\Response as StatusCode;

test('A api deve parar de responder quando atingir o limite de solicitações por minuto vindo de um ip', function () {
    User::factory()->create(['username' => 'thay']);

    $i = 0;
    while ($i < 60) {
        getJson('/api/users/thay')->assertStatus(StatusCode::HTTP_OK);
        $i++;
    }

    # After 60 requests, this should fail
    getJson('/api/users/thay')
        ->assertStatus(StatusCode::HTTP_TOO_MANY_REQUESTS)
        ->assertJsonFragment([
            "message" => "Too Many Attempts."
        ]);
});

test('A api deve parar de responder quando atingir o limite de solicitações por minuto vindo de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create(['username' => 'thay']);

    $i = 0;
    while ($i < 60) {
        actingAs($user)->getJson('/api/me')->assertStatus(StatusCode::HTTP_OK);
        $i++;
    }

    # After 60 requests, this should fail
    actingAs($user)
        ->getJson('/api/me')
        ->assertStatus(StatusCode::HTTP_TOO_MANY_REQUESTS)
        ->assertJsonFragment([
            "message" => "Too Many Attempts."
        ]);
});
