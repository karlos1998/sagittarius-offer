<?php

namespace App\Services;

use App\Enums\CartAction;
use App\Models\Ammunition;
use App\Models\Gun;
use App\Models\GunPackage;

class CartService
{
    /**
     * Get cart data from session
     */
    public function getCart(): array
    {
        return session()->get('cart', []);
    }

    /**
     * Get cart data with guns loaded from database
     */
    public function getCartWithGuns(): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return ['cart' => [], 'guns' => collect(), 'gunPackages' => collect()];
        }

        $gunIds = array_values(array_map(
            static fn ($item): int => (int) ($item['gun_id'] ?? 0),
            $cart
        ));

        $guns = Gun::with(['gunType', 'caliber.ammunitions'])->whereIn('id', $gunIds)->get();
        $packageIds = collect($cart)
            ->pluck('package_id')
            ->filter()
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
        $gunPackages = empty($packageIds)
            ? collect()
            : GunPackage::query()
                ->with([
                    'packageGuns' => fn ($query) => $query->with([
                        'gun.gunType',
                        'gun.caliber',
                        'ammunition',
                    ]),
                ])
                ->whereIn('id', $packageIds)
                ->get();

        return [
            'cart' => $cart,
            'guns' => $guns,
            'gunPackages' => $gunPackages,
        ];
    }

    /**
     * Add gun to cart
     */
    public function addGun(int $gunId): void
    {
        $gun = Gun::with(['gunType', 'caliber.ammunitions'])->findOrFail($gunId);

        $cart = $this->getCart();

        if (! isset($cart[$gunId])) {
            // Add new gun to cart with first available ammunition
            $firstAmmo = $gun->caliber->ammunitions->first();
            if ($firstAmmo) {
                $cart[$gunId] = [
                    'gun_id' => $gunId,
                    'ammunitions' => [
                        $firstAmmo->id => $this->resolveCartQuantityStep($firstAmmo),
                    ],
                    'package_id' => null,
                    'package_name' => null,
                    'package_guns' => [],
                ];
            }
        }

        session()->put('cart', $cart);
    }

    public function addPackage(int $packageId): void
    {
        $package = GunPackage::query()
            ->with([
                'packageGuns' => fn ($query) => $query
                    ->with([
                        'gun.caliber',
                        'ammunition',
                    ])
                    ->orderBy('sort_order'),
            ])
            ->findOrFail($packageId);

        $packageGuns = $package->packageGuns;

        if ($packageGuns->isEmpty()) {
            return;
        }

        $cart = $this->getCart();
        $packageGunDetails = $packageGuns
            ->map(function ($packageGun): ?string {
                if (! $packageGun->gun || ! $packageGun->ammunition) {
                    return null;
                }

                return sprintf(
                    '%s - %d strzałów (%s)',
                    $packageGun->gun->name,
                    (int) $packageGun->shots_quantity,
                    $packageGun->ammunition->name
                );
            })
            ->filter()
            ->values()
            ->all();

        foreach ($packageGuns as $packageGun) {
            if (! $packageGun->gun || ! $packageGun->ammunition) {
                continue;
            }

            $gunId = $packageGun->gun->id;
            $ammoId = $packageGun->ammunition->id;
            $shotsQuantity = max((int) $packageGun->shots_quantity, 1);

            if (! isset($cart[$gunId])) {
                $cart[$gunId] = [
                    'gun_id' => $gunId,
                    'ammunitions' => [
                        $ammoId => $shotsQuantity,
                    ],
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'package_guns' => $packageGunDetails,
                ];

                continue;
            }

            $cart[$gunId]['ammunitions'][$ammoId] = ($cart[$gunId]['ammunitions'][$ammoId] ?? 0) + $shotsQuantity;

            $this->attachPackageMeta($cart[$gunId], $package, $packageGunDetails);
        }

        session()->put('cart', $cart);
    }

    /**
     * Update cart item
     */
    public function updateCart(int $gunId, CartAction $action, ?int $ammoId = null): void
    {
        $cart = $this->getCart();

        if (! isset($cart[$gunId])) {
            throw new \InvalidArgumentException('Gun not found in cart');
        }

        switch ($action) {
            case CartAction::INCREASE:
                $this->increaseAmmunition($cart, $gunId, $ammoId);
                break;
            case CartAction::DECREASE:
                $this->decreaseAmmunition($cart, $gunId, $ammoId);
                break;
            case CartAction::REMOVE:
                $this->removeGun($cart, $gunId);
                break;
            case CartAction::ADD_AMMO:
                $this->addAmmunition($cart, $gunId, $ammoId);
                break;
            case CartAction::REMOVE_AMMO:
                $this->removeAmmunition($cart, $gunId, $ammoId);
                break;
            case CartAction::CHANGE_AMMO:
                $this->changeAmmunition($cart, $gunId, $ammoId);
                break;
        }

        session()->put('cart', $cart);
    }

    /**
     * Toggle club member status
     */
    public function toggleClubMember(bool $isClubMember): void
    {
        session()->put('is_club_member', $isClubMember);
    }

    /**
     * Get club member status
     */
    public function isClubMember(): bool
    {
        return session()->get('is_club_member', false);
    }

    /**
     * Clear cart
     */
    public function clearCart(): void
    {
        session()->forget('cart');
        session()->forget('is_club_member');
    }

    /**
     * Increase ammunition quantity
     */
    private function increaseAmmunition(array &$cart, int $gunId, ?int $ammoId): void
    {
        if ($ammoId && isset($cart[$gunId]['ammunitions'][$ammoId])) {
            $cart[$gunId]['ammunitions'][$ammoId] += $this->getAmmunitionCartQuantityStep($ammoId);
        } elseif ($ammoId) {
            $cart[$gunId]['ammunitions'][$ammoId] = $this->getAmmunitionCartQuantityStep($ammoId);
        }
    }

    /**
     * Decrease ammunition quantity
     */
    private function decreaseAmmunition(array &$cart, int $gunId, ?int $ammoId): void
    {
        if ($ammoId && isset($cart[$gunId]['ammunitions'][$ammoId])) {
            $cart[$gunId]['ammunitions'][$ammoId] = max(0, $cart[$gunId]['ammunitions'][$ammoId] - $this->getAmmunitionCartQuantityStep($ammoId));
            if ($cart[$gunId]['ammunitions'][$ammoId] == 0) {
                unset($cart[$gunId]['ammunitions'][$ammoId]);
                if (empty($cart[$gunId]['ammunitions'])) {
                    unset($cart[$gunId]);
                }
            }
        }
    }

    /**
     * Remove gun from cart
     */
    private function removeGun(array &$cart, int $gunId): void
    {
        unset($cart[$gunId]);
    }

    /**
     * Add ammunition to gun
     */
    private function addAmmunition(array &$cart, int $gunId, ?int $ammoId): void
    {
        if ($ammoId && ! isset($cart[$gunId]['ammunitions'][$ammoId])) {
            $cart[$gunId]['ammunitions'][$ammoId] = $this->getAmmunitionCartQuantityStep($ammoId);
        }
    }

    /**
     * Remove ammunition from gun
     */
    private function removeAmmunition(array &$cart, int $gunId, ?int $ammoId): void
    {
        if ($ammoId && isset($cart[$gunId]['ammunitions'][$ammoId])) {
            unset($cart[$gunId]['ammunitions'][$ammoId]);
            if (empty($cart[$gunId]['ammunitions'])) {
                unset($cart[$gunId]);
            }
        }
    }

    /**
     * Change ammunition for gun
     */
    private function changeAmmunition(array &$cart, int $gunId, ?int $ammoId): void
    {
        if ($ammoId && ! isset($cart[$gunId]['ammunitions'][$ammoId])) {
            $cart[$gunId]['ammunitions'][$ammoId] = $this->getAmmunitionCartQuantityStep($ammoId);
        }
    }

    private function getAmmunitionCartQuantityStep(int $ammoId): int
    {
        $ammunition = Ammunition::query()->findOrFail($ammoId);

        return $this->resolveCartQuantityStep($ammunition);
    }

    private function resolveCartQuantityStep(Ammunition $ammunition): int
    {
        return max((int) $ammunition->cart_quantity_step, 1);
    }

    /**
     * @param  array<string, mixed>  $cartItem
     * @param  array<int, string>  $packageGunNames
     */
    private function attachPackageMeta(array &$cartItem, GunPackage $package, array $packageGunNames): void
    {
        $cartItem['package_id'] = $package->id;
        $cartItem['package_name'] = $package->name;
        $cartItem['package_guns'] = $packageGunNames;
    }
}
