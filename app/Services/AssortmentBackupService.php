<?php

namespace App\Services;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\GunPackageGun;
use App\Models\GunType;
use App\Models\Instructor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

class AssortmentBackupService
{
    private const MODELS = [
        'gun_types' => GunType::class,
        'calibers' => Caliber::class,
        'ammunitions' => Ammunition::class,
        'guns' => Gun::class,
        'gun_packages' => GunPackage::class,
        'instructors' => Instructor::class,
        'package_guns' => GunPackageGun::class,
    ];

    public function create(): array
    {
        $id = (string) Str::uuid();
        $local = Storage::disk('local');
        $directory = 'assortment-backups/'.$id;
        $local->makeDirectory($directory);
        $zip = new ZipArchive;
        $archive = $local->path($directory.'/catalog.zip');

        if ($zip->open($archive, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Nie można utworzyć kopii katalogu.');
        }

        try {
            $records = (new Gun)->getConnection()->transaction(function (): array {
                $records = [];
                foreach (self::MODELS as $key => $model) {
                    $query = in_array($model, [Gun::class, Ammunition::class], true) ? $model::withTrashed() : $model::query();
                    $records[$key] = $query->orderBy('id')->lockForUpdate()->get()
                        ->map(fn ($record): array => $record->attributesToArray())->all();
                }

                return $records;
            });
            $paths = [];
            foreach ($records['guns'] as $gun) {
                array_push($paths, ...($gun['photos'] ?? []));
            }
            foreach ($records['gun_packages'] as $package) {
                $paths[] = $package['preview_image'];
            }
            foreach ($records['instructors'] as $instructor) {
                $paths[] = $instructor['photo'];
            }

            $media = [];
            $externalUrls = [];
            $disk = Storage::disk(config('filesystems.media_disk', 'public'));
            foreach (array_unique(array_filter($paths)) as $path) {
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    $externalUrls[] = $path;

                    continue;
                }

                $stream = $disk->readStream($path);
                if ($stream === false) {
                    throw new RuntimeException('Brak pliku zdjęcia w magazynie: '.$path);
                }
                $entry = 'media/'.count($media);
                try {
                    if (! $local->writeStream($directory.'/'.$entry, $stream)) {
                        throw new RuntimeException('Nie można skopiować zdjęcia do kopii.');
                    }
                } finally {
                    fclose($stream);
                }
                $file = $local->path($directory.'/'.$entry);
                $media[] = ['path' => $path, 'entry' => $entry, 'sha256' => hash_file('sha256', $file)];
                if (! $zip->addFile($file, $entry)) {
                    throw new RuntimeException('Nie można dodać zdjęcia do archiwum.');
                }
            }

            $manifest = ['version' => 1, 'backup_id' => $id, 'created_at' => now()->toIso8601String(),
                'records' => $records, 'media' => $media, 'external_urls' => $externalUrls];
            if (! $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)) || ! $zip->close()) {
                throw new RuntimeException('Nie można zapisać archiwum.');
            }
            $local->deleteDirectory($directory.'/media');

            return ['backup_id' => $id, 'counts' => array_map('count', $records), 'media_count' => count($media), 'external_urls' => $externalUrls];
        } catch (Throwable $exception) {
            if ($zip->status === ZipArchive::ER_OK) {
                @$zip->close();
            }
            $local->deleteDirectory($directory);

            throw $exception;
        }
    }

    public function path(string $id): string
    {
        abort_unless(Str::isUuid($id), 404);
        $local = Storage::disk('local');
        $path = 'assortment-backups/'.$id.'/catalog.zip';
        abort_unless($local->exists($path), 404);

        return $local->path($path);
    }

    public function restore(string $id): array
    {
        $zip = new ZipArchive;
        if ($zip->open($this->path($id)) !== true) {
            throw new RuntimeException('Nie można otworzyć kopii.');
        }
        $disk = Storage::disk(config('filesystems.media_disk', 'public'));
        $uploadedPaths = [];

        try {
            $manifest = json_decode($zip->getFromName('manifest.json'), true, flags: JSON_THROW_ON_ERROR);
            if (($manifest['version'] ?? null) !== 1 || array_keys($manifest['records'] ?? []) !== array_keys(self::MODELS)) {
                throw new RuntimeException('Nieobsługiwany format kopii.');
            }
            $mapping = [];
            foreach ($manifest['media'] as $media) {
                $content = $zip->getFromName($media['entry']);
                if ($content === false || ! hash_equals($media['sha256'], hash('sha256', $content))) {
                    throw new RuntimeException('Uszkodzony plik zdjęcia w kopii.');
                }
                $extension = pathinfo($media['path'], PATHINFO_EXTENSION);
                if (! preg_match('/^[a-zA-Z0-9]{1,10}$/', $extension)) {
                    throw new RuntimeException('Nieprawidłowe rozszerzenie zdjęcia w kopii.');
                }
                $path = 'guns/'.Str::uuid().'.'.$extension;
                if (! $disk->put($path, $content, 'public')) {
                    throw new RuntimeException('Nie można przywrócić zdjęcia.');
                }
                $uploadedPaths[] = $path;
                $mapping[$media['path']] = $path;
            }

            $records = $manifest['records'];
            foreach ($records['guns'] as &$gun) {
                $gun['photos'] = array_map(fn (string $path): string => $mapping[$path] ?? $path, $gun['photos'] ?? []);
            }
            unset($gun);
            foreach (['gun_packages' => 'preview_image', 'instructors' => 'photo'] as $key => $field) {
                foreach ($records[$key] as &$record) {
                    $record[$field] = $mapping[$record[$field]] ?? $record[$field];
                }
                unset($record);
            }

            (new Gun)->getConnection()->transaction(function () use ($records): void {
                Gun::query()->whereNotIn('id', array_column($records['guns'], 'id'))->delete();
                Ammunition::query()->whereNotIn('id', array_column($records['ammunitions'], 'id'))->delete();
                GunPackage::query()->whereNotIn('id', array_column($records['gun_packages'], 'id'))->update(['is_active' => false]);
                Instructor::query()->whereNotIn('id', array_column($records['instructors'], 'id'))->update(['is_active' => false]);
                GunPackageGun::query()->whereIn('gun_package_id', array_column($records['gun_packages'], 'id'))->delete();

                foreach (self::MODELS as $key => $model) {
                    foreach ($records[$key] as $row) {
                        $query = in_array($model, [Gun::class, Ammunition::class], true) ? $model::withTrashed() : $model::query();
                        $record = $query->find($row['id']) ?? new $model;
                        $record->timestamps = false;
                        $record->forceFill($row)->saveOrFail();
                    }
                }
            });

            return ['restored_backup_id' => $id, 'counts' => array_map('count', $records)];
        } catch (Throwable $exception) {
            if ($uploadedPaths !== []) {
                $disk->delete($uploadedPaths);
            }

            throw $exception;
        } finally {
            $zip->close();
        }
    }
}
