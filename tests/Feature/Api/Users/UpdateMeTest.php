<?php

use function Pest\Faker\faker;
use function Pest\Laravel\{patchJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    patchJson('/api/me')
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar um erro se tentar usar um username inválido', function () {
    /** @var \App\Models\User */
    $user2 = User::factory()->create();

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'username' => 'iago br',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.regex'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'username' => 'iago+bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.regex'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'username' => 'iago.bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.regex'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'username' => '@iagobruno',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.regex'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'username' => 'iago|bruno',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.regex'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'username' => 'criançafeliz',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.regex'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve retornar um erro se tentar usar um username que já está em uso', function () {
    $user1 = User::factory()->create(['username' => 'thay123']);
    /** @var \App\Models\User */
    $user2 = User::factory()->create();

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'username' => 'thay123',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.unique'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve retornar um erro se tentar usar um username mutio curto ou longo', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson('/api/me', [
            'username' => 'oi',
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.min.string'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user, 'sanctum')
        ->patchJson('/api/me', [
            'username' => str_repeat('a', 17),
        ])
        ->assertJsonValidationErrors([
            'username' => 'validation.max.string'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve retornar um erro se tentar usar um nome muito longo', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson('/api/me', [
            'name' => str_repeat('a', 256),
        ])
        ->assertJsonValidationErrors([
            'name' => 'validation.max.string'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve retornar um erro se tentar usar um email inválido', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->patchJson('/api/me', [
            'email' => 'nonemail',
        ])
        ->assertJsonValidationErrors([
            'email' => 'validation.email'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user, 'sanctum')
        ->patchJson('/api/me', [
            'email' => 'gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => 'validation.email'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);

    actingAs($user, 'sanctum')
        ->patchJson('/api/me', [
            'email' => '@gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => 'validation.email'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve retornar um erro se tentar usar um email que já está em uso', function () {
    $user1 = User::factory()->create(['email' => 'vini22@gmail.com']);
    /** @var \App\Models\User */
    $user2 = User::factory()->create();

    actingAs($user2, 'sanctum')
        ->patchJson('/api/me', [
            'email' => 'vini22@gmail.com',
        ])
        ->assertJsonValidationErrors([
            'email' => 'validation.unique'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
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
        ->patchJson('/api/me', $newData)
        ->assertStatus(StatusCode::HTTP_OK);

    $user->refresh();
    expect($user->toArray())->toMatchArray($newData);

    $this->assertDatabaseHas('users', $newData);
});
