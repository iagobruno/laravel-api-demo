<?php

use function Pest\Laravel\{getJson, actingAs};
use App\Models\User;
use Illuminate\Http\Response as StatusCode;

test('A api deve parar de responder quando atingir o limite de solicitações por minuto vindo de um ip', function () {
    User::factory()->create(['username' => 'thay']);

    $i = 0;
    while ($i < 60) {
        getJson('/api/users/thay')->assertOk();
        $i++;
    }

    # After 60 requests, this should fail
    getJson('/api/users/thay')
        ->assertStatus(StatusCode::HTTP_TOO_MANY_REQUESTS)
        ->assertJsonFragment([
            "message" => "Too many request attempts in too short a time."
        ]);
});

test('A api deve parar de responder quando atingir o limite de solicitações por minuto vindo de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create(['username' => 'thay']);
    /** @var \App\Models\User */
    $otherUser = User::factory()->create(['username' => 'vini']);

    $i = 0;
    while ($i < 60) {
        actingAs($user)->getJson(route('me.show'))->assertOk();
        $i++;
    }

    # After 60 requests, this should fail
    actingAs($user)->getJson(route('me.show'))
        ->assertStatus(StatusCode::HTTP_TOO_MANY_REQUESTS)
        ->assertJsonFragment([
            "message" => "Too many request attempts in too short a time."
        ]);
    // This must be successful
    actingAs($otherUser)->getJson(route('me.show'))
        ->assertOk()
        ->assertJsonMissing([
            "message" => "Too many request attempts in too short a time."
        ]);
});
