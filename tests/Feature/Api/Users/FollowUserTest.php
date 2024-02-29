<?php

use function Pest\Laravel\{putJson, actingAs};

use App\Events\UserFollowed;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    putJson(route('user.follow', ['fakeusername']))
        ->assertUnauthorized();
});

test('Deve retornar um erro se não conseguir encontrar o usuário pelo username', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->putJson(route('user.follow', ['fakeusername']))
        ->assertNotFound();
});

test('O token de acesso deve ter a permissão necessária', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToFollow = User::factory()->create();
    Sanctum::actingAs($user);

    putJson(route('user.follow', [$userToFollow->username]))
        ->assertJson([
            'message' => 'You don\'t have permission to perform this action.'
        ])
        ->assertForbidden();
});

test('Deve conseguir seguir outro usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToFollow = User::factory()->create();
    Sanctum::actingAs($user, ['followers:write']);

    putJson(route('user.follow', [$userToFollow->username]))
        ->assertOk();

    expect($user->isFollowing($userToFollow))->toBeTrue();
    expect($userToFollow->isFollowedBy($user))->toBeTrue();

    $this->assertDatabaseHas('followers', [
        'sender_id' => $user->id,
        'recipient_id' => $userToFollow->id
    ]);
});

test('Deve disparar um evento', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToFollow = User::factory()->create();
    Sanctum::actingAs($user, ['followers:write']);
    Event::fake(UserFollowed::class);

    putJson(route('user.follow', [$userToFollow->username]))
        ->assertOk();

    Event::assertDispatched(function (UserFollowed $event) use ($user, $userToFollow) {
        return $event->user->is($userToFollow) && $event->follower->is($user);
    });
});
