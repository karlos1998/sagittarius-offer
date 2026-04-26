<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Support\MediaUrlResolver;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $mediaUrlResolver = MediaUrlResolver::make();

        return Inertia::render('Home/Index', [
            'cart' => session()->get('cart', []),
            'instructors' => Instructor::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('full_name')
                ->get()
                ->map(fn (Instructor $instructor): array => [
                    'id' => $instructor->id,
                    'full_name' => $instructor->full_name,
                    'description' => $instructor->description,
                    'photo_url' => $mediaUrlResolver->url($instructor->photo),
                ])
                ->all(),
        ]);
    }
}
