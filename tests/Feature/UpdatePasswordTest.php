<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password cannot be updated through the removed starter endpoint', function () {
    $this->actingAs($user = User::factory()->create());

    $this->put('/user/password', [
        'current_password' => 'password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertNotFound();

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

test('removed password endpoint rejects an incorrect current password', function () {
    $this->actingAs($user = User::factory()->create());

    $response = $this->put('/user/password', [
        'current_password' => 'wrong-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertNotFound();

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

test('removed password endpoint rejects mismatched passwords', function () {
    $this->actingAs($user = User::factory()->create());

    $response = $this->put('/user/password', [
        'current_password' => 'password',
        'password' => 'new-password',
        'password_confirmation' => 'wrong-password',
    ]);

    $response->assertNotFound();

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});
