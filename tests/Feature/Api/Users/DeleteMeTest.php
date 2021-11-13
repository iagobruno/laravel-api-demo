<?php

use function Pest\Laravel\{deleteJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    deleteJson('/api/me')
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve conseguir deletar a conta do usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->deleteJson('/api/me')
        ->assertStatus(StatusCode::HTTP_OK);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id
    ]);
});
