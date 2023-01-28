<?php

use function Pest\Laravel\{get, post, postJson};

test('Test landing page', function () {
    get('/')
        ->assertOk()
        ->assertSee('Laravel');
});
