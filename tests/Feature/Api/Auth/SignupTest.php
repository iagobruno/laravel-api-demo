<?php

use function Pest\Laravel\{postJson};
use App\Models\User;

test('Deve conter todos os campos obrigatórios', function () {
    postJson(route('signup'), [])
        ->assertJsonValidationErrors([
            'email' => 'validation.required',
            'name' => 'validation.required',
            'username' => 'validation.required',
            'password' => 'validation.required',
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se o username for inválido', function () {
    postJson(route('signup'), [
        'username' => 'iago br',
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.alpha_dash'
        ])
        ->assertUnprocessable();

    postJson(route('signup'), [
        'username' => 'iago+bruno',
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.alpha_dash'
        ])
        ->assertUnprocessable();

    postJson(route('signup'), [
        'username' => 'iago.bruno',
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.alpha_dash'
        ])
        ->assertUnprocessable();

    postJson(route('signup'), [
        'username' => '@iagobruno',
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.alpha_dash'
        ])
        ->assertUnprocessable();

    postJson(route('signup'), [
        'username' => 'iago|bruno',
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.alpha_dash'
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se o username já estiver em uso', function () {
    User::factory()->create([
        'username' => 'fakeuser'
    ]);

    postJson(route('signup'), [
        'username' => 'fakeuser',
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.unique',
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se o username for muito curto ou longo', function () {
    postJson(route('signup'), [
        'username' => 'oi',
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.min',
        ])
        ->assertUnprocessable();

    postJson(route('signup'), [
        'username' => str_repeat('a', 17),
    ])
        ->assertJsonValidationErrors([
            'username' => 'validation.max',
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se o email já estiver em uso', function () {
    User::factory()->create([
        'email' => 'faker@gmail.com'
    ]);

    postJson(route('signup'), [
        'email' => 'faker@gmail.com'
    ])
        ->assertJsonValidationErrors([
            'email' => 'validation.unique',
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se tentar usar um email inválido', function () {
    postJson(route('signup'), [
        'email' => 'gmail.com'
    ])
        ->assertJsonValidationErrors([
            'email' => 'validation.email',
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se o name for muito longo', function () {
    postJson(route('signup'), [
        'name' => str_repeat('a', 300)
    ])
        ->assertJsonValidationErrors([
            'name' => 'validation.max',
        ])
        ->assertUnprocessable();
});

test('Deve retornar um erro se o password for muito curto ou longo', function () {
    postJson(route('signup'), [
        'password' => '1234'
    ])
        ->assertJsonValidationErrors([
            'password' => 'validation.min',
        ])
        ->assertUnprocessable();

    postJson(route('signup'), [
        'password' => str_repeat('a', 300)
    ])
        ->assertJsonValidationErrors([
            'password' => 'validation.max',
        ])
        ->assertUnprocessable();
});

test('Deve conseguir criar um usuário', function () {
    postJson(route('signup'), [
        'name' => 'faker',
        'username' => 'faker123',
        'email' => 'faker@gmail.com',
        'password' => '12345678',
    ])
        ->assertOk();

    $this->assertDatabaseHas('users', [
        'name' => 'faker',
        'username' => 'faker123',
        'email' => 'faker@gmail.com',
    ]);
});

test('Deve fazer hashing da senha do usuário', function () {
    postJson(route('signup'), [
        'name' => 'faker',
        'username' => 'faker123',
        'email' => 'faker@gmail.com',
        'password' => '12345678',
    ])
        ->assertOk();

    $user = User::where('username', 'faker123')->firstOrFail();

    expect($user->password)->not->toBe('12345678');
});

test('Deve retornar um token de api válido', function () {
    postJson(route('signup'), [
        'name' => 'faker',
        'username' => 'faker123',
        'email' => 'faker@gmail.com',
        'password' => '12345678',
    ])
        ->assertJsonStructure(['token'])
        ->assertOk();
});
