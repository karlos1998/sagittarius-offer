<?php

use App\Models\User;

test('profile information cannot be updated through the removed starter endpoint', function () {
    $this->actingAs($user = User::factory()->create());

    $this->put('/user/profile-information', [
        'name' => 'Test Name',
        'email' => 'test@example.com',
    ])->assertNotFound();

    expect($user->fresh())
        ->name->toEqual($user->name)
        ->email->toEqual($user->email);
});
