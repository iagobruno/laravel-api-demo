<?php

use function Pest\Laravel\{getJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;

test('Deve conseguir retornar as informações de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->getJson('/api/users/' . $user->username)
        ->assertExactJson($user->toArray())
        ->assertStatus(StatusCode::HTTP_OK);
});
