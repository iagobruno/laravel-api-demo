<?php

use function Pest\Laravel\{get, post, postJson};
use Illuminate\Http\Response as StatusCode;

test('Test landing page', function () {
    get('/')
        ->assertStatus(StatusCode::HTTP_OK)
        ->assertSee('Laravel');
});
