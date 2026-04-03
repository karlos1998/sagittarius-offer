<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddPackageToCartRequest;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\ToggleClubMemberRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Services\CartService;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index()
    {
        $cartData = $this->cartService->getCartWithGuns();

        return Inertia::render('Cart/Index', [
            'cart' => $cartData['cart'],
            'guns' => $cartData['guns'],
            'gunPackages' => $cartData['gunPackages'],
            'isClubMember' => $this->cartService->isClubMember(),
        ]);
    }

    public function add(AddToCartRequest $request)
    {
        $this->cartService->addGun($request->getGunId());

        return back()->with('success', 'Broń została dodana do koszyka');
    }

    public function addPackage(AddPackageToCartRequest $request)
    {
        $this->cartService->addPackage($request->getPackageId());

        return back()->with('success', 'Pakiet został dodany do koszyka');
    }

    public function update(UpdateCartRequest $request)
    {
        try {
            $this->cartService->updateCart(
                $request->getGunId(),
                $request->getAction(),
                $request->getAmmoId()
            );

            return back()->with('success', 'Koszyk został zaktualizowany');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleClubMember(ToggleClubMemberRequest $request)
    {
        $this->cartService->toggleClubMember($request->getIsClubMember());

        return back();
    }

    public function clear()
    {
        $this->cartService->clearCart();

        return back()->with('success', 'Koszyk został wyczyszczony');
    }
}
