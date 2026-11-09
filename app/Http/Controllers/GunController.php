<?php

namespace App\Http\Controllers;

use App\Http\Resources\Storefront\GunPackageResource;
use App\Http\Resources\Storefront\GunResource;
use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\GunType;
use App\Services\CartService;
use Inertia\Inertia;
use Inertia\Response;

class GunController extends Controller
{
    public function index(CartService $cartService): Response
    {
        $guns = GunResource::collection(
            Gun::with(['gunType', 'caliber.ammunitions'])->get()
        )->resolve();
        $gunTypes = GunType::all();
        $gunPackages = GunPackageResource::collection(
            GunPackage::query()
                ->available()
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
            'isClubMember' => $cartService->isClubMember(),
        ]);
    }
}
