<?php

use function Pest\Laravel\{deleteJson, actingAs};

use App\Events\TweetDeleted;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    deleteJson(route('tweet.delete', ['faketweetid9999']))
        ->assertUnauthorized();
});

test('Um usuário não pode deletar um tweet de outro usuário', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tweetFromUser2 = Tweet::factory()->fromUser($user2)->create();

    actingAs($user1, 'sanctum')
        ->deleteJson(route('tweet.delete', [$tweetFromUser2->id]))
        ->assertForbidden();

    $this->assertDatabaseHas('tweets', [
        'id' => $tweetFromUser2->id
    ]);
});

test('Deve conseguir deletar um tweet', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->fromUser($user)->create();

    $this->assertDatabaseHas('tweets', ['id' => $tweet->id]);

    actingAs($user, 'sanctum')
        ->deleteJson(route('tweet.delete', [$tweet->id]))
        ->assertJson([
            'success' => 'Successfully deleted!'
        ])
        ->assertOk();

    $this->assertDatabaseMissing('tweets', [
        'id' => $tweet->id
    ]);
});

test('Deve disparar um evento', function () {
    Event::fake(TweetDeleted::class);

    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->fromUser($user)->create();

    actingAs($user, 'sanctum')
        ->deleteJson(route('tweet.delete', [$tweet->id]))
        ->assertOk();

    Event::assertDispatched(function (TweetDeleted $event) use ($tweet) {
        return $event->tweet->is($tweet);
    });
});
