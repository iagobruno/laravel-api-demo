<?php

use function Pest\Laravel\{postJson, actingAs};
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    postJson(route('user.follow', ['fakeusername']))
        ->assertUnauthorized();
});

test('Deve retornar um erro se não conseguir encontrar o usuário pelo username', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson(route('user.follow', ['fakeusername']))
        ->assertNotFound();
});

test('Deve conseguir seguir outro usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToFollow = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson(route('user.follow', [$userToFollow->username]))
        ->assertOk();

    expect($user->isFollowing($userToFollow))->toBeTrue();
    expect($userToFollow->isFollowedBy($user))->toBeTrue();

    $this->assertDatabaseHas('followers', [
        'sender_id' => $user->id,
        'recipient_id' => $userToFollow->id
    ]);
});
