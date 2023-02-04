<?php

use function Pest\Laravel\{postJson};
use App\Models\User;

test('Deve retornar um erro se não houver um username ou password', function () {
    postJson(route('signin'), [])
        ->assertJsonValidationErrors([
            'username' => 'Required when email is not present.',
            'email' => 'Required when username is not present.',
            'password' => 'Required field.',
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro 401 se o username não existir', function () {
    postJson(route('signin'), [
        'username' => 'faker1234',
        'password' => '12345678'
    ])
        ->assertUnauthorized();
});

test('Deve retornar um erro 401 se a senha estiver incorreta', function () {
    $user = User::factory()->create([
        'password' => 'another_pass'
    ]);

    postJson(route('signin'), [
        'username' => $user->username,
        'password' => '12345678'
    ])
        ->assertUnauthorized();
});

test('Deve conseguir logar um usuário pelo username', function () {
    User::factory()->create([
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ]);

    postJson(route('signin'), [
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ])
        ->assertJsonStructure(['user'])
        ->assertOk();
});

test('Deve conseguir logar um usuário pelo email', function () {
    User::factory()->create([
        'email' => 'randomuser@gmail.com',
        'password' => 'strongpass_1234'
    ]);

    postJson(route('signin'), [
        'email' => 'randomuser@gmail.com',
        'password' => 'strongpass_1234'
    ])
        ->assertJsonStructure(['user'])
        ->assertOk();
});

test('Deve retornar um token de api', function () {
    User::factory()->create([
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ]);

    postJson(route('signin'), [
        'username' => 'randomuser',
        'password' => 'strongpass_1234'
    ])
        ->assertJsonStructure(['token', 'user'])
        ->assertOk();
});
