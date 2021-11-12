<?php

use function Pest\Laravel\{postJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use function Pest\Faker\faker;
use App\Models\Tweet;
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    postJson('/api/tweets', [])
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar um erro se a solicitação não conter todos os campos obrigatórios', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson('/api/tweets', [])
        ->assertJsonValidationErrors([
            'content' => 'validation.required'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve retornar um erro se o content for maior que 140 caracteres', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson('/api/tweets', [
            'content' => str_repeat('a', 141)
        ])
        ->assertJsonValidationErrors([
            'content' => 'validation.max.string'
        ])
        ->assertStatus(StatusCode::HTTP_UNPROCESSABLE_ENTITY);
});

test('Deve conseguir criar um novo tweet', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    $content = faker()->text(140);

    actingAs($user, 'sanctum')
        ->postJson('/api/tweets', [
            'content' => $content,
        ])
        ->assertStatus(StatusCode::HTTP_CREATED);

    $this->assertDatabaseHas('tweets', [
        'content' => $content,
    ]);
});

test('Deve associar corretamente ao usuário logado', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    $content = faker()->text(140);

    actingAs($user, 'sanctum')
        ->postJson('/api/tweets', [
            'content' => $content,
        ])
        ->assertStatus(StatusCode::HTTP_CREATED);

    $tweet = Tweet::where('content', $content)->firstOrFail();
    expect($tweet->user_id)->toBe($user->id);
});
