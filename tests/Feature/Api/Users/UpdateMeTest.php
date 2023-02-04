<?php

use function Pest\Faker\faker;
use function Pest\Laravel\{patchJson, actingAs};
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    patchJson(route('user.update'))
        ->assertUnauthorized();
});

test('Deve retornar um erro se tentar usar um username inválido', function () {
    /** @var \App\Models\User */
    $user2 = User::factory()->create();

    actingAs($user2, 'sanctum')
        ->patchJson(route('user.update'), [
            'username' => 'iago br',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('user.update'), [
            'username' => 'iago+bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('user.update'), [
            'username' => 'iago.bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('user.update'), [
            'username' => '@iagobruno',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.alpha_dash')
        ])
        ->assertUnprocessable();

    actingAs($user2, 'sanctum')
        ->patchJson(route('user.update'), [
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
        ->patchJson(route('user.update'), [
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
        ->patchJson(route('user.update'), [
            'username' => 'oi',
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.min.string', ['min' => 4])
        ])
        ->assertUnprocessable();

    actingAs($user, 'sanctum')
        ->patchJson(route('user.update'), [
            'username' => str_repeat('a', 17),
        ])
        ->assertJsonValidationErrors([
            'username' => __('validation.max.string', ['max' => 16])
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um nome muito longo', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson(route('user.update'), [
            'name' => str_repeat('a', 256),
        ])
        ->assertJsonValidationErrors([
            'name' => __('validation.max.string', ['max' => 255])
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um email inválido', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson(route('user.update'), [
            'email' => 'nonemail',
        ])
        ->assertJsonValidationErrors([
            'email' => __('validation.email')
        ])
        ->assertUnprocessable();

    actingAs($user, 'sanctum')
        ->patchJson(route('user.update'), [
            'email' => 'gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => __('validation.email')
        ])
        ->assertUnprocessable();

    actingAs($user, 'sanctum')
        ->patchJson(route('user.update'), [
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
        ->patchJson(route('user.update'), [
            'email' => 'vini22@gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => __('validation.unique')
        ])
        ->assertUnprocessable();
});

test('Deve conseguir atualizar as informações do usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $newData = [
        'username' => 'new_username_123',
        'name' => faker()->name(),
        'email' => faker()->email(),
    ];

    actingAs($user, 'sanctum')
        ->patchJson(route('user.update'), $newData)
        ->assertOk();

    $user->refresh();
    expect($user->toArray())->toMatchArray($newData);

    $this->assertDatabaseHas('users', $newData);
});
