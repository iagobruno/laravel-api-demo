<?php

use function Pest\Laravel\{deleteJson, actingAs};
use Illuminate\Http\Response as StatusCode;
use App\Models\Tweet;
use App\Models\User;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    deleteJson('/api/tweets/999')
        ->assertStatus(StatusCode::HTTP_UNAUTHORIZED);
});

test('Deve retornar um erro se um usuário tentar deletar um tweet de outro usuário', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tweetFromUser2 = Tweet::factory()->create(['user_id' => $user2->id]);

    actingAs($user1, 'sanctum')
        ->deleteJson('/api/tweets/' . $tweetFromUser2->id)
        ->assertStatus(StatusCode::HTTP_FORBIDDEN);

    $this->assertDatabaseHas('tweets', [
        'id' => $tweetFromUser2->id
    ]);
});

test('Deve conseguir deletar um tweet', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create(['user_id' => $user->id]);

    $this->assertDatabaseHas('tweets', ['id' => $tweet->id]);

    actingAs($user, 'sanctum')
        ->deleteJson('/api/tweets/' . $tweet->id)
        ->assertJson([
            'message' => 'Successfully deleted!'
        ])
        ->assertStatus(StatusCode::HTTP_OK);

    $this->assertDatabaseMissing('tweets', [
        'id' => $tweet->id
    ]);
});
