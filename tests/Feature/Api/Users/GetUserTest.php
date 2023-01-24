<?php

use function Pest\Laravel\{getJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;

test('Deve retornar um erro se o username não existir', function () {
    getJson(route('user.get', ['fakeusername']))
        ->assertStatus(StatusCode::HTTP_NOT_FOUND);
});

test('Deve conseguir retornar as informações de um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    getJson(route('user.get', [$user->username]))
        ->assertJson($user->toArray())
        ->assertStatus(StatusCode::HTTP_OK);
});

test('Deve informar se o usuário logado segue o usuário solicitado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $user->forceFollow($otherUser);

    actingAs($user, 'sanctum')
        ->getJson(route('user.get', [$otherUser->username]))
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
    $user2->forceFollow($user1);
    $user3->forceFollow($user1);

    getJson(route('user.get', [$user1->username]))
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
    $user1->forceFollow($user2);
    $user1->forceFollow($user3);

    getJson(route('user.get', [$user1->username]))
        ->assertJson([
            'following_count' => 2
        ])
        ->assertStatus(StatusCode::HTTP_OK);
});
