<?php

use function Pest\Laravel\{postJson};
use Illuminate\Http\Response as StatusCode;
use App\Models\User;

test('Deve retornar um erro se não houver um username ou password', function () {
    postJson('/api/signin', [])
        ->assertJsonValidationErrors([
            'username' => 'validation.required_without',
            'email' => 'validation.required_without',
            'password' => 'validation.required',
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve retornar um erro 401 se o username não existir', function () {
    postJson('/api/signin', [
        'username' => 'faker1234',
        'password' => '12345678'
    ])
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar um erro 401 se a senha estiver incorreta', function () {
    $user = User::factory()->create([
        'password' => 'another_pass'
    ]);

    postJson('/api/signin', [
        'username' => $user->username,
        'password' => '12345678'
    ])
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve conseguir logar um usuário pelo username', function () {
    User::factory()->create([
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ]);

    postJson('/api/signin', [
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ])
        ->assertJsonStructure(['user'])
        ->assertStatus(StatusCode::HTTP_OK);
});

test('Deve conseguir logar um usuário pelo email', function () {
    User::factory()->create([
        'email' => 'randomuser@gmail.com',
        'password' => 'strongpass_1234'
    ]);

    postJson('/api/signin', [
        'email' => 'randomuser@gmail.com',
        'password' => 'strongpass_1234'
    ])
        ->assertJsonStructure(['user'])
        ->assertStatus(StatusCode::HTTP_OK);
});

test('Deve retornar um token de api', function () {
    User::factory()->create([
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ]);

    postJson('/api/signin', [
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ])
        ->assertJsonStructure(['token', 'user'])
        ->assertStatus(StatusCode::HTTP_OK);
});
