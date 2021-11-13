<?php

use function Pest\Laravel\{postJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    postJson("/api/users/username/follow")
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar um erro se não conseguir encontrar o usuário pelo username', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson("/api/users/fakeusername/follow")
        ->assertStatus(StatusCode::HTTP_NOT_FOUND);
});

test('Deve conseguir seguir outro usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToFollow = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson("/api/users/{$userToFollow->username}/follow")
        ->assertStatus(StatusCode::HTTP_OK);

    expect($user->isFollowing($userToFollow))->toBeTrue();
    expect($userToFollow->isFollowedBy($user))->toBeTrue();

    $this->assertDatabaseHas('followers', [
        'sender_id' => $user->id,
        'recipient_id' => $userToFollow->id
    ]);
});
