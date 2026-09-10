<?php

use App\Filament\Resources\Guns\Pages\EditGun;
use App\Models\Ammunition;
use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\Instructor;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\AssortmentBackupService;
use App\Services\CartService;
use Filament\Actions\DeleteAction;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('local');
    Storage::fake('public');
    config(['filesystems.media_disk' => 'public']);
    $user = User::factory()->create();
    $this->credentials = ['email' => $user->email, 'password' => 'password'];
});

it('soft deletes through API while preserving completed order snapshots and relationships', function (): void {
    $gun = Gun::factory()->create();
    $item = OrderItem::factory()->create(['gun_id' => $gun->id, 'gun_name' => 'Historical name']);
    $item->order->update(['is_completed' => true]);
    $before = $item->fresh()->getAttributes();

    $this->postJson('/api/assortment/guns/'.$gun->id.'/delete', $this->credentials)->assertOk();
    $this->assertSoftDeleted($gun);
    expect($item->fresh()->getAttributes())->toBe($before);
    expect($item->fresh()->gun->id)->toBe($gun->id);
    $this->postJson('/api/assortment', $this->credentials)->assertJsonCount(0, 'guns');
    $this->get('/guns')->assertInertia(fn (Assert $page) => $page->has('guns', 0));
    $this->post('/cart/add', ['gun_id' => $gun->id])->assertNotFound();
    $this->postJson('/api/assortment/guns/'.$gun->id.'/restore', $this->credentials)->assertOk();
    expect($gun->fresh()->trashed())->toBeFalse();
});

it('hides incomplete packages and removes them from existing carts', function (): void {
    $guns = Gun::factory()->count(2)->create();
    $ammo = Ammunition::factory()->create();
    $package = GunPackage::factory()->create();
    foreach ($guns as $gun) {
        $package->guns()->attach($gun, ['ammunition_id' => $ammo->id, 'shots_quantity' => 10]);
    }
    app(CartService::class)->addPackage($package->id);
    expect(app(CartService::class)->getCart())->toHaveCount(2);
    $guns[0]->delete();

    expect(GunPackage::query()->available()->count())->toBe(0);
    expect(app(CartService::class)->getCartWithGuns()['cart'])->toBe([]);
    $this->post('/cart/add-package', ['package_id' => $package->id])->assertNotFound();
    expect($package->packageGuns()->count())->toBe(2);
    $guns[0]->restore();
    expect(GunPackage::query()->available()->count())->toBe(1);
});

it('uses soft deletion from Filament', function (): void {
    $user = User::factory()->create();
    $admin = new class($user->getAttributes()) extends User implements FilamentUser
    {
        public function canAccessPanel(Panel $panel): bool
        {
            return true;
        }
    };
    $admin->exists = true;
    $this->actingAs($admin);
    $gun = Gun::factory()->create();
    Livewire::test(EditGun::class, ['record' => $gun->id])->callAction(DeleteAction::class);
    $this->assertSoftDeleted($gun);
    Livewire::test(EditGun::class, ['record' => $gun->id])->callAction(\Filament\Actions\RestoreAction::class);
    expect($gun->fresh()->trashed())->toBeFalse();
});

it('backs up catalog and media and restores IDs prices packages and archived records without changing orders', function (): void {
    Storage::disk('public')->put('guns/original.jpg', 'original image bytes');
    $gun = Gun::factory()->create(['name' => 'Original name', 'photos' => ['guns/original.jpg']]);
    $archived = Gun::factory()->create();
    $archived->delete();
    $ammo = Ammunition::factory()->create(['club_price' => 5, 'standard_price' => 6]);
    $package = GunPackage::factory()->create(['name' => 'Original package', 'preview_image' => 'guns/original.jpg']);
    $package->guns()->attach($gun, ['ammunition_id' => $ammo->id, 'shots_quantity' => 10]);
    $instructor = Instructor::factory()->create(['photo' => 'guns/original.jpg']);
    $item = OrderItem::factory()->create(['gun_id' => $gun->id, 'ammunition_id' => $ammo->id, 'gun_package_id' => $package->id]);
    $before = $item->fresh()->getAttributes();

    $response = $this->postJson('/api/assortment/backups', $this->credentials)->assertCreated()->assertJsonPath('media_count', 1);
    $id = $response->json('backup_id');
    $this->postJson('/api/assortment/backups/'.$id.'/download', $this->credentials)->assertOk()->assertDownload('assortment-'.$id.'.zip');
    $zip = new ZipArchive;
    $zip->open(app(AssortmentBackupService::class)->path($id));
    expect($zip->getFromName('media/0'))->toBe('original image bytes');
    $zip->close();

    $gun->update(['name' => 'Changed', 'photos' => []]);
    $gun->delete();
    $ammo->update(['club_price' => 99]);
    $package->packageGuns()->delete();
    $package->update(['name' => 'Changed package']);
    Storage::disk('public')->delete('guns/original.jpg');
    $newGun = Gun::factory()->create();
    $newAmmo = Ammunition::factory()->create();
    $newItem = OrderItem::factory()->create(['gun_id' => $newGun->id, 'ammunition_id' => $newAmmo->id]);
    $newPackage = GunPackage::factory()->create();
    $newInstructor = Instructor::factory()->create();

    $this->postJson('/api/assortment/backups/'.$id.'/restore', $this->credentials)->assertOk();
    expect($gun->fresh()->name)->toBe('Original name');
    expect($gun->fresh()->trashed())->toBeFalse();
    expect($archived->fresh()->trashed())->toBeTrue();
    expect($ammo->fresh()->club_price)->toBe('5.00');
    expect($package->fresh()->name)->toBe('Original package');
    expect($package->packageGuns()->first()->shots_quantity)->toBe(10);
    expect(Storage::disk('public')->get($gun->fresh()->photos[0]))->toBe('original image bytes');
    expect($instructor->fresh()->photo)->toBe($gun->fresh()->photos[0]);
    expect($item->fresh()->getAttributes())->toBe($before);
    $this->assertSoftDeleted($newGun);
    $this->assertSoftDeleted($newAmmo);
    expect($newItem->fresh()->gun->id)->toBe($newGun->id);
    expect($newItem->fresh()->ammunition->id)->toBe($newAmmo->id);
    expect($newPackage->fresh()->is_active)->toBeFalse();
    expect($newInstructor->fresh()->is_active)->toBeFalse();
});

it('requires authentication for deletion restoration and all backup routes', function (): void {
    $gun = Gun::factory()->create();
    $backup = app(AssortmentBackupService::class)->create()['backup_id'];
    foreach (['/guns/'.$gun->id.'/delete', '/guns/'.$gun->id.'/restore', '/backups', '/backups/'.$backup.'/download', '/backups/'.$backup.'/restore'] as $path) {
        $this->postJson('/api/assortment'.$path)->assertUnauthorized();
    }
    expect($gun->fresh()->trashed())->toBeFalse();
});

it('does not publish a backup when a source image is missing', function (): void {
    Gun::factory()->create(['photos' => ['guns/missing.jpg']]);
    $this->postJson('/api/assortment/backups', $this->credentials)->assertServerError();
    expect(Storage::disk('local')->allFiles())->toBe([]);
});

it('rolls back restoration and cleans new media on database failure', function (): void {
    Storage::disk('public')->put('guns/original.jpg', 'original');
    $gun = Gun::factory()->create(['photos' => ['guns/original.jpg']]);
    $id = app(AssortmentBackupService::class)->create()['backup_id'];
    $gun->update(['name' => 'Keep changed name']);
    Gun::saving(function (): void {
        throw new RuntimeException('Simulated restore failure');
    });
    try {
        $this->postJson('/api/assortment/backups/'.$id.'/restore', $this->credentials)->assertServerError();
        expect($gun->fresh()->name)->toBe('Keep changed name');
        expect(Storage::disk('public')->allFiles())->toBe(['guns/original.jpg']);
    } finally {
        Gun::flushEventListeners();
    }
});

it('rejects corrupted backup media without changing the catalog', function (): void {
    Storage::disk('public')->put('guns/original.jpg', 'original');
    $gun = Gun::factory()->create(['photos' => ['guns/original.jpg']]);
    $id = app(AssortmentBackupService::class)->create()['backup_id'];
    $gun->update(['name' => 'Keep current name']);
    $zip = new ZipArchive;
    $zip->open(app(AssortmentBackupService::class)->path($id));
    $zip->addFromString('media/0', 'corrupted');
    $zip->close();

    $this->postJson('/api/assortment/backups/'.$id.'/restore', $this->credentials)->assertServerError();
    expect($gun->fresh()->name)->toBe('Keep current name');
    expect(Storage::disk('public')->allFiles())->toBe(['guns/original.jpg']);
});

it('copies images from the configured media disk into the private backup', function (): void {
    Storage::fake('s3');
    config(['filesystems.media_disk' => 's3']);
    Storage::disk('s3')->put('guns/example.jpg', 'S3 photo');
    Gun::factory()->create(['photos' => ['guns/example.jpg']]);
    $id = $this->postJson('/api/assortment/backups', $this->credentials)->assertCreated()->json('backup_id');
    $zip = new ZipArchive;
    $zip->open(app(AssortmentBackupService::class)->path($id));
    expect($zip->getFromName('media/0'))->toBe('S3 photo');
    $zip->close();
});
