<?php

namespace App\Http\Controllers;

use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\GunType;
use Inertia\Inertia;

class GunController extends Controller
{
    public function index()
    {
        $guns = Gun::with(['gunType', 'caliber.ammunitions'])->get();
        $gunTypes = GunType::all();
        $gunPackages = GunPackage::query()
            ->where('is_active', true)
            ->with([
                'packageGuns' => fn ($query) => $query->with([
                    'gun.gunType',
                    'gun.caliber',
                    'ammunition',
                ]),
            ])
            ->orderBy('name')
            ->get();

        return Inertia::render('Guns/Index', [
            'guns' => $guns,
            'gunTypes' => $gunTypes,
            'gunPackages' => $gunPackages,
            'cart' => session()->get('cart', []),
        ]);
    }
}
