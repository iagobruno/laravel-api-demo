<?php

use function Pest\Laravel\{deleteJson, actingAs};

use App\Events\TweetDeleted;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    deleteJson(route('tweet.destroy', ['faketweetid9999']))
        ->assertUnauthorized();
});

test('Um usuário não pode deletar um tweet de outro usuário', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tweetFromUser2 = Tweet::factory()->fromUser($user2)->create();

    actingAs($user1, 'sanctum')
        ->deleteJson(route('tweet.destroy', [$tweetFromUser2->id]))
        ->assertForbidden();

    $this->assertDatabaseHas('tweets', [
        'id' => $tweetFromUser2->id
    ]);
});

test('O token de acesso deve ter a permissão "tweet:write"', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->fromUser($user)->create();
    Sanctum::actingAs($user);

    deleteJson(route('tweet.destroy', [$tweet->id]))
        ->assertJson([
            'message' => 'You don\'t have permission to perform this action.'
        ])
        ->assertForbidden();
});

test('Deve conseguir deletar um tweet', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->fromUser($user)->create();
    Sanctum::actingAs($user, ['tweet:write']);

    $this->assertDatabaseHas('tweets', ['id' => $tweet->id]);

    deleteJson(route('tweet.destroy', [$tweet->id]))
        ->assertJson([
            'success' => 'Successfully deleted!'
        ])
        ->assertOk();

    $this->assertDatabaseMissing('tweets', [
        'id' => $tweet->id
    ]);
});

test('Deve disparar um evento', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->fromUser($user)->create();
    Event::fake(TweetDeleted::class);
    Sanctum::actingAs($user, ['tweet:write']);

    deleteJson(route('tweet.destroy', [$tweet->id]))
        ->assertOk();

    Event::assertDispatched(function (TweetDeleted $event) use ($tweet) {
        return $event->tweet->is($tweet);
    });
});
