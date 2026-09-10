<?php

use App\Models\Gun;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
    config(['filesystems.media_disk' => 'public']);
    $user = User::factory()->create();
    config(['assortment.email' => $user->email]);
    $this->credentials = ['email' => $user->email, 'password' => 'password'];
    $this->gun = Gun::factory()->create(['photos' => null]);
    $this->endpoint = '/api/assortment/guns/'.$this->gun->id.'/photos';
});

it('uploads photos for a gun without photos using multipart authentication', function (): void {
    $response = $this->post($this->endpoint, [
        ...$this->credentials,
        'photos' => [UploadedFile::fake()->image('gun.jpg'), UploadedFile::fake()->image('gun.png')],
    ], ['Accept' => 'application/json'])->assertOk()->assertJsonPath('id', $this->gun->id)->assertJsonCount(2, 'photos');

    expect($this->gun->fresh()->photos)->toBe($response->json('photos'));
    foreach ($response->json('photos') as $index => $path) {
        Storage::disk('public')->assertExists($path);
        expect(Storage::disk('public')->getVisibility($path))->toBe('public');
        $response->assertJsonPath('photo_urls.'.$index, '/storage/'.$path);
    }
});

it('appends by default and replaces only when requested', function (): void {
    Storage::disk('public')->put('guns/old.jpg', 'old');
    $this->gun->update(['photos' => ['guns/old.jpg']]);
    $this->post($this->endpoint, [...$this->credentials, 'photos' => [UploadedFile::fake()->image('new.jpg')]])
        ->assertOk()->assertJsonCount(2, 'photos')->assertJsonPath('photos.0', 'guns/old.jpg');

    $this->post($this->endpoint, [
        ...$this->credentials, 'mode' => 'replace', 'photos' => [UploadedFile::fake()->image('replacement.jpg')],
    ])->assertOk()->assertJsonCount(1, 'photos');
    expect($this->gun->fresh()->photos)->not->toContain('guns/old.jpg');
    Storage::disk('public')->assertExists('guns/old.jpg');
});

it('rejects invalid credentials without writing files', function (): void {
    $this->post($this->endpoint, [
        ...$this->credentials, 'password' => 'wrong', 'photos' => [UploadedFile::fake()->image('gun.jpg')],
    ])->assertUnauthorized();
    expect(Storage::disk('public')->allFiles())->toBe([]);
    expect($this->gun->fresh()->photos)->toBeNull();
});

it('rejects invalid uploads', function (string $kind): void {
    $file = match ($kind) {
        'text' => UploadedFile::fake()->createWithContent('fake.jpg', '<?php echo "not an image";'),
        'svg' => UploadedFile::fake()->createWithContent('gun.svg', '<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>'),
        'large' => UploadedFile::fake()->image('gun.jpg')->size(20481),
    };
    $this->post($this->endpoint, [...$this->credentials, 'photos' => [$file]])
        ->assertUnprocessable()->assertJsonValidationErrors('photos.0');
    expect(Storage::disk('public')->allFiles())->toBe([]);
})->with(['text', 'svg', 'large']);

it('enforces the total gallery limit before writing files', function (): void {
    $existing = array_map(fn (int $index): string => 'guns/'.$index.'.jpg', range(1, 10));
    $this->gun->update(['photos' => $existing]);
    $this->post($this->endpoint, [...$this->credentials, 'photos' => [UploadedFile::fake()->image('gun.jpg')]])
        ->assertUnprocessable()->assertJsonValidationErrors('photos');
    expect($this->gun->fresh()->photos)->toBe($existing);
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

it('rejects missing photos and unsupported modes', function (): void {
    $this->post($this->endpoint, $this->credentials)->assertUnprocessable()->assertJsonValidationErrors('photos');
    $this->post($this->endpoint, [
        ...$this->credentials, 'mode' => 'delete', 'photos' => [UploadedFile::fake()->image('gun.jpg')],
    ])->assertUnprocessable()->assertJsonValidationErrors('mode');
});

it('uses the configured media disk', function (): void {
    Storage::fake('s3');
    config(['filesystems.media_disk' => 's3']);
    $response = $this->post($this->endpoint, [...$this->credentials, 'photos' => [UploadedFile::fake()->image('gun.jpg')]])->assertOk();
    Storage::disk('s3')->assertExists($response->json('photos.0'));
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

it('cleans uploaded files and preserves the gallery when database save fails', function (): void {
    $this->gun->update(['photos' => ['guns/old.jpg']]);
    Gun::saving(function (): void {
        throw new RuntimeException('Simulated database failure');
    });

    try {
        $this->post($this->endpoint, [
            ...$this->credentials, 'mode' => 'replace', 'photos' => [UploadedFile::fake()->image('gun.jpg')],
        ])->assertServerError();
        expect($this->gun->fresh()->photos)->toBe(['guns/old.jpg']);
        expect(Storage::disk('public')->allFiles())->toBe([]);
    } finally {
        Gun::flushEventListeners();
    }
});

it('does not change the gallery when storage fails partway through', function (): void {
    $this->gun->update(['photos' => ['guns/old.jpg']]);
    $disk = Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
    $disk->shouldReceive('putFile')->once()->andReturn('guns/partial.jpg');
    $disk->shouldReceive('putFile')->once()->andReturn(false);
    $disk->shouldReceive('delete')->once()->with(['guns/partial.jpg'])->andReturn(true);
    Storage::shouldReceive('disk')->with('public')->andReturn($disk);

    $this->post($this->endpoint, [
        ...$this->credentials, 'mode' => 'replace',
        'photos' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
    ])->assertServerError();
    expect($this->gun->fresh()->photos)->toBe(['guns/old.jpg']);
});

it('returns not found for a missing gun', function (): void {
    $this->post('/api/assortment/guns/999999/photos', [
        ...$this->credentials, 'photos' => [UploadedFile::fake()->image('gun.jpg')],
    ], ['Accept' => 'application/json'])->assertNotFound();
    expect(Storage::disk('public')->allFiles())->toBe([]);
});
