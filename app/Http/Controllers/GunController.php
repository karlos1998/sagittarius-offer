<?php

namespace App\Http\Controllers;

use App\Models\Gun;
use App\Models\GunType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GunController extends Controller
{
    public function index()
    {
        $guns = Gun::with(['gunType', 'caliber.ammunitions'])->get();
        $gunTypes = GunType::all();

        return Inertia::render('Guns/Index', [
            'guns' => $guns,
            'gunTypes' => $gunTypes
        ]);
    }
}
