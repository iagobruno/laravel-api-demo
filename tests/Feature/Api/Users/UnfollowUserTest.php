<?php

use function Pest\Laravel\{postJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    postJson(route('user.unfollow', ['fakeusername']))
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar um erro se não conseguir encontrar o usuário pelo username', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson(route('user.unfollow', ['fakeusername']))
        ->assertStatus(StatusCode::HTTP_NOT_FOUND);
});

test('Deve conseguir parar de seguir um usuário', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToUnfollow = User::factory()->create();
    $user->forceFollow($userToUnfollow);

    actingAs($user, 'sanctum')
        ->postJson(route('user.unfollow', [$userToUnfollow->username]))
        ->assertStatus(StatusCode::HTTP_OK);

    expect($user->isFollowing($userToUnfollow))->toBeFalse();
    expect($userToUnfollow->isFollowedBy($user))->toBeFalse();

    $this->assertDatabaseMissing('followers', [
        'sender_id' => $user->id,
        'recipient_id' => $userToUnfollow->id
    ]);
});

test('Deve invalidar o cache da lista de contas que o usuário logado segue ', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $userToUnfollow = User::factory()->create();

    $cacheKey = $user->id . '-following-ids';
    Cache::put($cacheKey, [$userToUnfollow->id]);

    actingAs($user, 'sanctum')
        ->postJson(route('user.unfollow', [$userToUnfollow->username]))
        ->assertStatus(StatusCode::HTTP_OK);

    expect(Cache::has($cacheKey))->toBeFalse();
    expect(Cache::get($cacheKey))->toBeNull();
});
