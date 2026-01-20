<?php

namespace App\Http\Controllers;

use App\Models\Gun;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GunController extends Controller
{
    public function index()
    {
        $guns = Gun::with(['gunType', 'caliber.ammunitions'])->get();

        return Inertia::render('Guns/Index', [
            'guns' => $guns
        ]);
    }
}
