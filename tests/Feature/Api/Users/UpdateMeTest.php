<?php

use function Pest\Faker\fake;
use function Pest\Laravel\{patchJson, actingAs};

use App\Events\UserUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    patchJson(route('me.update'))
        ->assertUnauthorized();
});

test('Deve retornar um erro se tentar usar um username inválido', function () {
    /** @var \App\Models\User */
    $user2 = User::factory()->create();

    actingAs($user2, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => 'iago br',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => 'iago+bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => 'iago.bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => '@iagobruno',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => 'iago|bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um username que já está em uso', function () {
    $user1 = User::factory()->create(['username' => 'thay123']);
    /** @var \App\Models\User */
    $user2 = User::factory()->create();

    actingAs($user2, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => 'thay123',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.unique')
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um username mutio curto ou longo', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => 'oi',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.min.string', ['min' => 4])
        ])
        ->assertUnprocessable();

    actingAs($user, 'sanctum')
        ->patchJson(route('me.update'), [
            'username' => str_repeat('a', 21),
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.max.string', ['max' => 20])
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um nome muito longo', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson(route('me.update'), [
            'name' => str_repeat('a', 51),
        ])
        ->assertJsonValidationErrors([
            'name' => __('validation.max.string', ['max' => 50])
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um email inválido', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson(route('me.update'), [
            'email' => 'nonemail',
        ])
        ->assertJsonValidationErrors([
            'email' => __('validation.email')
        ])
        ->assertUnprocessable();

    actingAs($user, 'sanctum')
        ->patchJson(route('me.update'), [
            'email' => 'gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => __('validation.email')
        ])
        ->assertUnprocessable();

    actingAs($user, 'sanctum')
        ->patchJson(route('me.update'), [
            'email' => '@gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => __('validation.email')
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um email que já está em uso', function () {
    $user1 = User::factory()->create(['email' => 'vini22@gmail.com']);
    /** @var \App\Models\User */
    $user2 = User::factory()->create();

    actingAs($user2, 'sanctum')
        ->patchJson(route('me.update'), [
            'email' => 'vini22@gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => __('validation.unique')
        ])
        ->assertUnprocessable();
});

test('O token de acesso deve ter a permissão necessária', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $newData = [
        'username' => 'new_username_123',
        'name' => fake()->name(),
        'email' => fake()->email(),
    ];

    patchJson(route('me.update'), $newData)
        ->assertJson([
            'message' => 'You don\'t have permission to perform this action.'
        ])
        ->assertForbidden();
});

test('Deve conseguir atualizar as informações do usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['profile:write']);
    $newData = [
        'username' => 'new_username_123',
        'name' => fake()->name(),
        'email' => fake()->email(),
    ];

    patchJson(route('me.update'), $newData)
        ->assertOk();

    $this->assertDatabaseHas('users', $newData);
});

test('Deve disparar um evento', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $newData = [
        'username' => 'new_username_123',
    ];
    Sanctum::actingAs($user, ['profile:write']);
    Event::fake(UserUpdated::class);

    patchJson(route('me.update'), $newData)
        ->assertOk();

    Event::assertDispatched(function (UserUpdated $event) use ($user, $newData) {
        return $event->user->is($user) && $event->user->username === $newData['username'];
    });
});
