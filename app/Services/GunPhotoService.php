<?php

namespace App\Services;

use App\Models\Gun;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class GunPhotoService
{
    /** @param array<int, UploadedFile> $photos */
    public function upload(Gun $gun, array $photos, bool $replace): Gun
    {
        $disk = Storage::disk(config('filesystems.media_disk', 'public'));
        $uploadedPaths = [];

        try {
            return $gun->getConnection()->transaction(function () use ($gun, $photos, $replace, $disk, &$uploadedPaths): Gun {
                $lockedGun = Gun::query()->lockForUpdate()->findOrFail($gun->id);
                $existingPhotos = $replace ? [] : ($lockedGun->photos ?? []);

                if (count($existingPhotos) + count($photos) > 10) {
                    throw ValidationException::withMessages(['photos' => 'Broń może mieć maksymalnie 10 zdjęć.']);
                }

                foreach ($photos as $photo) {
                    $path = $disk->putFile('guns', $photo, 'public');

                    if ($path === false) {
                        throw new RuntimeException('Nie udało się zapisać zdjęcia.');
                    }

                    $uploadedPaths[] = $path;
                }

                $lockedGun->photos = array_merge($existingPhotos, $uploadedPaths);
                $lockedGun->saveOrFail();

                return $lockedGun;
            });
        } catch (Throwable $exception) {
            if ($uploadedPaths !== []) {
                $disk->delete($uploadedPaths);
            }

            throw $exception;
        }
    }
}
