<?php

use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

it('does not expose starter authentication or account routes', function (string $method, string $path): void {
    $this->call($method, $path)->assertNotFound();
    $this->actingAs(User::factory()->create())->call($method, $path)->assertNotFound();
})->with([
    ['GET', '/dashboard'], ['GET', '/register'], ['POST', '/register'],
    ['GET', '/login'], ['POST', '/login'], ['POST', '/logout'],
    ['GET', '/forgot-password'], ['POST', '/forgot-password'],
    ['GET', '/reset-password/token'], ['POST', '/reset-password'],
    ['GET', '/two-factor-challenge'], ['POST', '/two-factor-challenge'],
    ['GET', '/user/confirm-password'], ['POST', '/user/confirm-password'],
    ['GET', '/user/confirmed-password-status'], ['GET', '/user/profile'],
    ['PUT', '/user/profile-information'], ['PUT', '/user/password'],
    ['POST', '/user/two-factor-authentication'], ['DELETE', '/user/two-factor-authentication'],
    ['GET', '/user/two-factor-qr-code'], ['GET', '/user/two-factor-secret-key'],
    ['GET', '/user/two-factor-recovery-codes'], ['POST', '/user/two-factor-recovery-codes'],
    ['POST', '/user/confirmed-two-factor-authentication'],
    ['DELETE', '/user/other-browser-sessions'], ['DELETE', '/user'],
    ['DELETE', '/user/profile-photo'], ['GET', '/user/api-tokens'],
    ['POST', '/user/api-tokens'], ['PUT', '/user/api-tokens/1'], ['DELETE', '/user/api-tokens/1'],
]);

it('does not register any Fortify or Jetstream controller routes', function (): void {
    foreach (Route::getRoutes() as $route) {
        expect($route->getActionName())->not->toContain('Laravel\\Fortify\\', 'Laravel\\Jetstream\\');
    }
    expect(Route::has('login'))->toBeFalse();
    expect(Route::has('register'))->toBeFalse();
    expect(Route::has('dashboard'))->toBeFalse();
});

it('cannot create an account through the removed registration endpoint', function (): void {
    $this->post('/register', [
        'name' => 'Visitor', 'email' => 'visitor@example.com',
        'password' => 'Password123!', 'password_confirmation' => 'Password123!',
    ])->assertNotFound();

    $this->assertDatabaseMissing('users', ['email' => 'visitor@example.com']);
});

it('keeps Filament login working', function (): void {
    config(['app.env' => 'local']);
    $user = User::factory()->create();
    $this->get(route('filament.admin.auth.login'))->assertOk();
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(Login::class)->fillForm([
        'email' => $user->email, 'password' => 'password',
    ])->call('authenticate')->assertHasNoFormErrors()->assertRedirect();

    $this->assertAuthenticatedAs($user, 'web');
    $this->get('/panel')->assertOk();
});

it('logs out through Filament and protects the employee panel again', function (): void {
    config(['app.env' => 'local']);
    $this->actingAs(User::factory()->create());
    $this->post(route('filament.admin.auth.logout'))
        ->assertRedirect(route('filament.admin.auth.login'));
    $this->assertGuest('web');
    $this->get('/panel')->assertRedirect(route('filament.admin.auth.login'));
});
