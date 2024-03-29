<?php

use function Pest\Laravel\{deleteJson, actingAs};

use App\Events\UserUnfollowed;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    deleteJson(route('user.unfollow', ['fakeusername']))
        ->assertUnauthorized();
});

test('Deve retornar um erro se não conseguir encontrar o usuário pelo username', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->deleteJson(route('user.unfollow', ['fakeusername']))
        ->assertNotFound();
});

test('O token de acesso deve ter a permissão necessária', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToFollow = User::factory()->create();
    Sanctum::actingAs($user);

    deleteJson(route('user.follow', [$userToFollow->username]))
        ->assertJson([
            'message' => 'You don\'t have permission to perform this action.'
        ])
        ->assertForbidden();
});

test('Deve conseguir parar de seguir um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToUnfollow = User::factory()->create();
    $user->forceFollow($userToUnfollow);
    Sanctum::actingAs($user, ['followers:write']);

    deleteJson(route('user.unfollow', [$userToUnfollow->username]))
        ->assertOk();

    expect($user->isFollowing($userToUnfollow))->toBeFalse();
    expect($userToUnfollow->isFollowedBy($user))->toBeFalse();

    $this->assertDatabaseMissing('followers', [
        'sender_id' => $user->id,
        'recipient_id' => $userToUnfollow->id
    ]);
});

test('Deve disparar um evento', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToUnfollow = User::factory()->create();
    $user->forceFollow($userToUnfollow);
    Event::fake(UserUnfollowed::class);
    Sanctum::actingAs($user, ['followers:write']);

    deleteJson(route('user.unfollow', [$userToUnfollow->username]))
        ->assertOk();

    Event::assertDispatched(function (UserUnfollowed $event) use ($user, $userToUnfollow) {
        return $event->user->is($userToUnfollow) && $event->follower->is($user);
    });
});
