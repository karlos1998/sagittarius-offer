<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAssortmentRequest;
use App\Http\Requests\UploadGunPhotosRequest;
use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunType;
use App\Services\GunPhotoService;
use App\Support\MediaUrlResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class AssortmentController extends Controller
{
    public function deleteGun(Gun $gun): JsonResponse
    {
        $gun->delete();

        return response()->json(['id' => $gun->id, 'deleted_at' => $gun->deleted_at]);
    }

    public function restoreGun(string $gun): JsonResponse
    {
        $record = Gun::withTrashed()->findOrFail($gun);
        $record->restore();

        return response()->json(['id' => $record->id, 'deleted_at' => null]);
    }

    public function uploadPhotos(UploadGunPhotosRequest $request, Gun $gun, GunPhotoService $service): JsonResponse
    {
        $gun = $service->upload($gun, $request->getPhotos(), $request->replacesPhotos());

        return response()->json([
            'id' => $gun->id,
            'photos' => $gun->photos,
            'photo_urls' => MediaUrlResolver::make()->many($gun->photos),
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'guns' => Gun::query()->with(['gunType', 'caliber'])->orderBy('id')->get(),
            'ammunitions' => Ammunition::query()->with('caliber')->orderBy('id')->get(),
        ]);
    }

    public function import(ImportAssortmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = (new Gun)->getConnection()->transaction(function () use ($data): array {
            $result = ['guns' => [], 'ammunitions' => []];

            foreach ($data['guns'] ?? [] as $row) {
                $gun = isset($row['id']) ? Gun::query()->findOrFail($row['id']) : new Gun;
                $gun->fill(Arr::only($row, ['name', 'description']));
                $gun->caliber()->associate(Caliber::query()->firstOrCreate(['name' => $row['caliber']]));
                $gun->gunType()->associate(GunType::query()->firstOrCreate(['name' => $row['gun_type']]));
                $gun->save();
                $result['guns'][] = $gun->fresh(['gunType', 'caliber']);
            }

            foreach ($data['ammunitions'] ?? [] as $row) {
                $ammunition = isset($row['id']) ? Ammunition::query()->findOrFail($row['id']) : new Ammunition;
                $ammunition->fill(Arr::only($row, ['name', 'club_price', 'standard_price', 'cart_quantity_step']));
                $ammunition->caliber()->associate(Caliber::query()->firstOrCreate(['name' => $row['caliber']]));
                $ammunition->save();
                $result['ammunitions'][] = $ammunition->fresh('caliber');
            }

            return $result;
        });

        return response()->json($result);
    }
}
