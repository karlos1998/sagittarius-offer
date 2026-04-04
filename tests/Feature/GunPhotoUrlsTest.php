<?php

use App\Support\MediaUrlResolver;

it('builds public urls for relative gun photos', function () {
    $resolver = MediaUrlResolver::make();

    expect($resolver->many([
        'guns/example.jpg',
        'https://cdn.example.com/photo.jpg',
    ]))->toBe([
        '/storage/guns/example.jpg',
        'https://cdn.example.com/photo.jpg',
    ]);
});
