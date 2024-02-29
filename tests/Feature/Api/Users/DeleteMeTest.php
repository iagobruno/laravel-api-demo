<?php

use function Pest\Laravel\{deleteJson, actingAs};

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    deleteJson(route('me.destroy'))
        ->assertUnauthorized();
});

test('O token de acesso deve ter a permissão necessária', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    deleteJson(route('me.destroy'))
        ->assertJson([
            'message' => 'You don\'t have permission to perform this action.'
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $user->id
    ]);
});

test('Deve conseguir deletar a conta do usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['profile:write']);

    deleteJson(route('me.destroy'))
        ->assertOk();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id
    ]);
});

test('Deve disparar um evento', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['profile:write']);
    Event::fake(UserDeleted::class);

    deleteJson(route('me.destroy'))
        ->assertOk();

    Event::assertDispatched(function (UserDeleted $event) use ($user) {
        return $event->user->is($user);
    });
});
