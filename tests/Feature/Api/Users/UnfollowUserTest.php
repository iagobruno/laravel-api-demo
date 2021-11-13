<?php

use function Pest\Laravel\{postJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    postJson("/api/users/username/unfollow")
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar um erro se não conseguir encontrar o usuário pelo username', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson("/api/users/fakeusername/unfollow")
        ->assertStatus(StatusCode::HTTP_NOT_FOUND);
});

test('Deve conseguir parar de seguir um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToUnfollow = User::factory()->create();
    $user->follow($userToUnfollow);
    $userToUnfollow->acceptFollowRequestFrom($user);

    actingAs($user, 'sanctum')
        ->postJson("/api/users/{$userToUnfollow->username}/unfollow")
        ->assertStatus(StatusCode::HTTP_OK);

    expect($user->isFollowing($userToUnfollow))->toBeFalse();
    expect($userToUnfollow->isFollowedBy($user))->toBeFalse();

    $this->assertDatabaseMissing('followers', [
        'sender_id' => $user->id,
        'recipient_id' => $userToUnfollow->id
    ]);
});
