<?php

use App\Models\Gun;

it('builds public urls for relative gun photos', function () {
    $gun = new Gun([
        'photos' => [
            'guns/example.jpg',
            'https://cdn.example.com/photo.jpg',
        ],
    ]);

    expect($gun->photo_urls)->toBe([
        '/storage/guns/example.jpg',
        'https://cdn.example.com/photo.jpg',
    ]);
});
