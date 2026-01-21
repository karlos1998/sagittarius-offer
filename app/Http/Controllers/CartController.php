<?php

namespace App\Http\Controllers;

use App\Models\Gun;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $guns = collect();

        if (!empty($cart)) {
            $gunIds = array_keys($cart);
            $guns = Gun::with(['gunType', 'caliber.ammunitions'])->whereIn('id', $gunIds)->get();
        }

        return Inertia::render('Cart/Index', [
            'cart' => $cart,
            'guns' => $guns,
            'isClubMember' => session()->get('is_club_member', false),
        ]);
    }

    public function add(Request $request)
    {
        $gunId = $request->gun_id;
        $gun = Gun::with(['gunType', 'caliber.ammunitions'])->findOrFail($gunId);

        $cart = session()->get('cart', []);

        if (!isset($cart[$gunId])) {
            // Dodaj nową broń do koszyka z pierwszą dostępną amunicją
            $firstAmmo = $gun->caliber->ammunitions->first();
            if ($firstAmmo) {
                $cart[$gunId] = [
                    'gun_id' => $gunId,
                    'ammo_id' => $firstAmmo->id,
                    'quantity' => 5, // zawsze 5 strzałów
                ];
            }
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Broń została dodana do koszyka');
    }

    public function update(Request $request)
    {
        $gunId = $request->gun_id;
        $action = $request->action; // 'increase', 'decrease', 'remove'
        $ammoId = $request->ammo_id;

        $cart = session()->get('cart', []);

        if (!isset($cart[$gunId])) {
            return back()->with('error', 'Broń nie została znaleziona w koszyku');
        }

        switch ($action) {
            case 'increase':
                $cart[$gunId]['quantity'] += 5;
                break;
            case 'decrease':
                $cart[$gunId]['quantity'] = max(0, $cart[$gunId]['quantity'] - 5);
                if ($cart[$gunId]['quantity'] == 0) {
                    unset($cart[$gunId]);
                }
                break;
            case 'change_ammo':
                if ($ammoId) {
                    $cart[$gunId]['ammo_id'] = $ammoId;
                }
                break;
            case 'remove':
                unset($cart[$gunId]);
                break;
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Koszyk został zaktualizowany');
    }

    public function toggleClubMember(Request $request)
    {
        $isClubMember = $request->is_club_member;
        session()->put('is_club_member', $isClubMember);

        return back();
    }

    public function clear()
    {
        session()->forget('cart');
        session()->forget('is_club_member');

        return back()->with('success', 'Koszyk został wyczyszczony');
    }
}
