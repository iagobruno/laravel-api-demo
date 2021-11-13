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

    getJson('/api/users/' . $user->username)
        ->assertJson($user->toArray())
        ->assertStatus(StatusCode::HTTP_OK);
});

test('Deve informar se o usuário logado segue o usuário solicitado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $user->follow($otherUser);
    $otherUser->acceptFollowRequestFrom($user);

    actingAs($user, 'sanctum')
        ->getJson('/api/users/' . $otherUser->username)
        ->assertJson([
            'viewer_follows' => true
        ])
        ->assertStatus(StatusCode::HTTP_OK);
});

test('Deve informar o número de seguidores', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $user2->follow($user1);
    $user1->acceptFollowRequestFrom($user2);
    $user3->follow($user1);
    $user1->acceptFollowRequestFrom($user3);

    getJson('/api/users/' . $user1->username)
        ->assertJson([
            'followers_count' => 2
        ])
        ->assertStatus(StatusCode::HTTP_OK);
});

test('Deve informar o número de pessoas que o usuário segue', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $user1->follow($user2);
    $user2->acceptFollowRequestFrom($user1);
    $user1->follow($user3);
    $user3->acceptFollowRequestFrom($user1);

    getJson('/api/users/' . $user1->username)
        ->assertJson([
            'following_count' => 2
        ])
        ->assertStatus(StatusCode::HTTP_OK);
});
