<?php

use App\Models\User;

test('starter browser session management is unavailable', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->delete('/user/other-browser-sessions', [
        'password' => 'password',
    ]);

    $response->assertNotFound();
});
