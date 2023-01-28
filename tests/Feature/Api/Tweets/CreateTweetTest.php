<?php

use function Pest\Laravel\{postJson, actingAs};
use function Pest\Faker\faker;
use App\Models\Tweet;
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    postJson(route('tweet.store'), [])
        ->assertUnauthorized();
});

test('Deve conter todos os campos obrigatórios', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson(route('tweet.store'), [])
        ->assertJsonValidationErrors([
            'content' => 'validation.required'
        ])
        ->assertUnprocessable();
});

test('O campo "content" não pode ter mais que 140 caracteres', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson(route('tweet.store'), [
            'content' => str_repeat('a', 141)
        ])
        ->assertJsonValidationErrors([
            'content' => 'validation.max.string'
        ])
        ->assertUnprocessable();
});

test('Deve conseguir criar um novo tweet', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    $content = faker()->text(140);

    actingAs($user, 'sanctum')
        ->postJson(route('tweet.store'), [
            'content' => $content,
        ])
        ->assertJson([
            'data' => compact('content'),
        ])
        ->assertCreated();

    $this->assertDatabaseHas('tweets', [
        'content' => $content,
    ]);
});

test('Deve associar corretamente ao usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    $content = faker()->text(140);

    actingAs($user, 'sanctum')
        ->postJson(route('tweet.store'), [
            'content' => $content,
        ])
        ->assertCreated();

    $tweet = Tweet::where('content', $content)->firstOrFail();
    expect($tweet->user_id)->toBe($user->id);
});
