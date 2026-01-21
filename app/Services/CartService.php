<?php

namespace App\Services;

use App\Enums\CartAction;
use App\Models\Gun;
use Illuminate\Http\Request;

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
            return ['cart' => [], 'guns' => collect()];
        }

        $gunIds = array_keys($cart);
        $guns = Gun::with(['gunType', 'caliber.ammunitions'])->whereIn('id', $gunIds)->get();

        return [
            'cart' => $cart,
            'guns' => $guns,
        ];
    }

    /**
     * Add gun to cart
     */
    public function addGun(int $gunId): void
    {
        $gun = Gun::with(['gunType', 'caliber.ammunitions'])->findOrFail($gunId);

        $cart = $this->getCart();

        if (!isset($cart[$gunId])) {
            // Add new gun to cart with first available ammunition
            $firstAmmo = $gun->caliber->ammunitions->first();
            if ($firstAmmo) {
                $cart[$gunId] = [
                    'gun_id' => $gunId,
                    'ammunitions' => [
                        $firstAmmo->id => 5 // always 5 shots for first ammunition
                    ]
                ];
            }
        }

        session()->put('cart', $cart);
    }

    /**
     * Update cart item
     */
    public function updateCart(int $gunId, CartAction $action, ?int $ammoId = null): void
    {
        $cart = $this->getCart();

        if (!isset($cart[$gunId])) {
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
            $cart[$gunId]['ammunitions'][$ammoId] += 5;
        } elseif ($ammoId) {
            $cart[$gunId]['ammunitions'][$ammoId] = 5;
        }
    }

    /**
     * Decrease ammunition quantity
     */
    private function decreaseAmmunition(array &$cart, int $gunId, ?int $ammoId): void
    {
        if ($ammoId && isset($cart[$gunId]['ammunitions'][$ammoId])) {
            $cart[$gunId]['ammunitions'][$ammoId] = max(0, $cart[$gunId]['ammunitions'][$ammoId] - 5);
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
        if ($ammoId && !isset($cart[$gunId]['ammunitions'][$ammoId])) {
            $cart[$gunId]['ammunitions'][$ammoId] = 5;
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
        if ($ammoId && !isset($cart[$gunId]['ammunitions'][$ammoId])) {
            $cart[$gunId]['ammunitions'][$ammoId] = 5;
        }
    }
}
