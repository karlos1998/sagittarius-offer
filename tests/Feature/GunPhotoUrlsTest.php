<?php

use App\Support\MediaUrlResolver;

it('builds public urls for relative gun photos', function () {
    config(['filesystems.media_disk' => 'public', 'filesystems.disks.public.url' => '/storage']);
    $resolver = MediaUrlResolver::make();

    expect($resolver->many([
        'guns/example.jpg',
        'https://cdn.example.com/photo.jpg',
    ]))->toBe([
        '/storage/guns/example.jpg',
        'https://cdn.example.com/photo.jpg',
    ]);
});
