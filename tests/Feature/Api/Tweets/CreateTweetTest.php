<?php

use function Pest\Laravel\{postJson, actingAs};
use function Pest\Faker\fake;

use App\Events\TweetCreated;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

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
            'content' => __('validation.required')
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
            'content' => __('validation.max.string', ['max' => 140])
        ])
        ->assertUnprocessable();
});

test('O token de acesso deve ter a permissão "tweet:write"', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $content = fake()->text(140);
    Sanctum::actingAs($user);

    postJson(route('tweet.store'), [
        'content' => $content,
    ])
        ->assertJson([
            'message' => 'You don\'t have permission to perform this action.'
        ])
        ->assertForbidden();
});

test('Deve conseguir criar um novo tweet', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $content = fake()->text(140);
    Sanctum::actingAs($user, ['tweet:write']);

    postJson(route('tweet.store'), [
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
    $content = fake()->text(140);
    Sanctum::actingAs($user, ['tweet:write']);

    postJson(route('tweet.store'), [
            'content' => $content,
        ])
        ->assertCreated();

    $tweet = Tweet::where('content', $content)->firstOrFail();
    expect($tweet->user_id)->toBe($user->id);
});

test('Deve disparar um evento', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $content = fake()->text(140);
    Sanctum::actingAs($user, ['tweet:write']);
    Event::fake(TweetCreated::class);

    postJson(route('tweet.store'), [
            'content' => $content,
        ])
        ->assertCreated();

    Event::assertDispatched(function (TweetCreated $event) use ($content) {
        return $event->tweet->content === $content;
    });
});
