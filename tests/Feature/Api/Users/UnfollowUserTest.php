<?php

use function Pest\Laravel\{postJson, actingAs};

use App\Events\UserUnfollowed;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    postJson(route('user.unfollow', ['fakeusername']))
        ->assertUnauthorized();
});

test('Deve retornar um erro se não conseguir encontrar o usuário pelo username', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson(route('user.unfollow', ['fakeusername']))
        ->assertNotFound();
});

test('Deve conseguir parar de seguir um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToUnfollow = User::factory()->create();
    $user->forceFollow($userToUnfollow);

    actingAs($user, 'sanctum')
        ->postJson(route('user.unfollow', [$userToUnfollow->username]))
        ->assertOk();

    expect($user->isFollowing($userToUnfollow))->toBeFalse();
    expect($userToUnfollow->isFollowedBy($user))->toBeFalse();

    $this->assertDatabaseMissing('followers', [
        'sender_id' => $user->id,
        'recipient_id' => $userToUnfollow->id
    ]);
});

test('Deve disparar um evento', function () {
    Event::fake(UserUnfollowed::class);

    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToUnfollow = User::factory()->create();
    $user->forceFollow($userToUnfollow);

    actingAs($user, 'sanctum')
        ->postJson(route('user.unfollow', [$userToUnfollow->username]))
        ->assertOk();

    Event::assertDispatched(function (UserUnfollowed $event) use ($user, $userToUnfollow) {
        return $event->user->is($userToUnfollow) && $event->follower->is($user);
    });
});
