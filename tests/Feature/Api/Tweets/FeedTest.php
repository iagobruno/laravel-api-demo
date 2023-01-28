<?php

use function Pest\Laravel\{getJson, actingAs};
use App\Models\Tweet;
use App\Models\User;
use Carbon\Carbon;

test('Deve retornar um erro se a solicitação não houver um token', function () {
    getJson(route('feed'))
        ->assertUnauthorized();
});

test('Deve retornar uma resposta paginada', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $user1->forceFollow($user2);
    $user1->forceFollow($user3);

    actingAs($user1, 'sanctum')
        ->getJson(route('feed'))
        ->assertJsonStructure(['data', 'links', 'meta'])
        ->assertOk();
});

test('Deve retornar os tweets das pessoas que o usuário logado segue', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $userToNotIncludeInResponse = User::factory()->create();
    $user1->forceFollow($user2);
    $user1->forceFollow($user3);
    Tweet::factory()->times(3)->fromUser($user2)->create();
    Tweet::factory()->times(4)->fromUser($user3)->create();
    Tweet::factory()->times(4)->fromUser($userToNotIncludeInResponse)->create();

    $response = actingAs($user1, 'sanctum')
        ->getJson(route('feed'))
        ->assertOk()
        ->getData();

    expect($response->data)->toBeArray();
    foreach ($response->data as $tweet) {
        expect($tweet->user_id)->toBeIn([$user2->id, $user3->id]);
        expect($tweet->user_id)->not->toBe($userToNotIncludeInResponse->id);
    }
    expect($response->data)->toHaveLength(7);
});

test('Deve retornar também os tweets do usuário logado', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user1->forceFollow($user2);
    Tweet::factory()->times(2)->fromUser($user1)->create();
    Tweet::factory()->times(3)->fromUser($user2)->create();

    $response = actingAs($user1, 'sanctum')
        ->getJson(route('feed'))
        ->assertOk()
        ->getData();

    $hasUser1Tweets = array_some($response->data, fn ($item) => $item->user_id === $user1->id);
    expect($hasUser1Tweets)->toBeTrue();
    expect($response->data)->toHaveLength(5);
});

test('Deve retornar as informações do autor de cada tweet', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user1->forceFollow($user2);
    Tweet::factory()->times(3)->fromUser($user2)->create();

    $response = actingAs($user1, 'sanctum')
        ->getJson(route('feed'))
        ->assertOk()
        ->getData();

    foreach ($response->data as $tweet) {
        expect($tweet)->toHaveKey('user');
        expect($tweet->user)->toMatchArray($user2->toArray());
    }
});

test('Deve permitir fazer paginação', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $tweet1 = Tweet::factory()->fromUser($user)->create();
    $tweet2 = Tweet::factory()->fromUser($user)->create();

    $page = 1;
    $response1 = actingAs($user, 'sanctum')
        ->getJson(route('feed', ['page' => $page, 'perPage' => 1]))
        ->assertOk()
        ->getData();

    expect($response1->data)->toBeArray();
    expect($response1->data)->toHaveLength(1);
    expect($response1->data[0])->toMatchArray($tweet1->toArray());

    $page = 2;
    $response2 = actingAs($user, 'sanctum')
        ->getJson(route('feed', ['page' => $page, 'perPage' => 1]))
        ->assertOk()
        ->getData();

    expect($response2->data)->toBeArray();
    expect($response2->data)->toHaveLength(1);
    expect($response2->data[0])->toMatchArray($tweet2->toArray());
});

test('Deve retornar os tweets em ordem cronológica', function () {
    /** @var \App\Models\User */
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $user1->forceFollow($user2);
    $user1->forceFollow($user3);
    Tweet::factory()->fromUser($user3)->create(['created_at' => now()->subDays(7)]);
    Tweet::factory()->fromUser($user2)->create(['created_at' => now()]);
    Tweet::factory()->fromUser($user2)->create(['created_at' => now()->subDays(5)]);
    Tweet::factory()->fromUser($user1)->create(['created_at' => now()->subDays(2)]);

    $tweets = actingAs($user1, 'sanctum')
        ->getJson(route('feed'))
        ->assertOk()
        ->getData()
        ->data;

    $prevDate = null;
    foreach ($tweets as $tweet) {
        // Run only first time
        if (!$prevDate) {
            $prevDate = Carbon::parse($tweet->created_at);
            continue;
        }

        $currentDate = Carbon::parse($tweet->created_at);
        $currentTweetIsMoreRecentThanPrevious = $currentDate <= $prevDate;

        expect($currentTweetIsMoreRecentThanPrevious)->toBeTrue();

        $prevDate = Carbon::parse($tweet->created_at);
    }
});





/**
 * Checks if any item in array satisfies the condition
 * @return bool
 */
function array_some($array, $callback)
{
    foreach ($array as $item) {
        if ($callback($item) === true) {
            return true;
        }
    }
    return false;
}
