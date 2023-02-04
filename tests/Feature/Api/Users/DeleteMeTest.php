<?php

use function Pest\Laravel\{deleteJson, actingAs};

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    deleteJson(route('user.destroy'))
        ->assertUnauthorized();
});

test('Deve conseguir deletar a conta do usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->deleteJson(route('user.destroy'))
        ->assertOk();

    $this->assertDatabaseMissing('users', [
        'id' => $user->id
    ]);
});

test('Deve disparar um evento', function () {
    Event::fake(UserDeleted::class);

    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->deleteJson(route('user.destroy'))
        ->assertOk();

    Event::assertDispatched(function (UserDeleted $event) use ($user) {
        return $event->user->is($user);
    });
});
