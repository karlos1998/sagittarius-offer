<?php

namespace App\Http\Controllers;

use App\Http\Resources\Storefront\GunPackageResource;
use App\Http\Resources\Storefront\GunResource;
use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\GunType;
use Inertia\Inertia;

class GunController extends Controller
{
    public function index()
    {
        $guns = GunResource::collection(
            Gun::with(['gunType', 'caliber.ammunitions'])->get()
        )->resolve();
        $gunTypes = GunType::all();
        $gunPackages = GunPackageResource::collection(
            GunPackage::query()
                ->where('is_active', true)
                ->with([
                    'packageGuns' => fn ($query) => $query->with([
                        'gun.gunType',
                        'gun.caliber',
                        'ammunition',
                    ]),
                ])
                ->orderBy('name')
                ->get()
        )->resolve();

        return Inertia::render('Guns/Index', [
            'guns' => $guns,
            'gunTypes' => $gunTypes,
            'gunPackages' => $gunPackages,
            'cart' => session()->get('cart', []),
        ]);
    }
}
