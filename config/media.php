<?php

return [
    'max_file_size' => env('MEDIA_MAX_FILE_SIZE', 10240),
    'max_dimensions' => env('MEDIA_MAX_DIMENSIONS', 2048),
    'allowed_types' => array_map('trim', explode(',', env('MEDIA_ALLOWED_TYPES', 'jpg,jpeg,png,webp'))),
    
    'avatar' => [
        'max_file_size' => env('AVATAR_MAX_FILE_SIZE', 2048),
        'max_dimensions' => env('AVATAR_MAX_DIMENSIONS', 1024),
        'allowed_types' => array_map('trim', explode(',', env('AVATAR_ALLOWED_TYPES', 'jpg,jpeg,png,webp'))),
    ],
    
    'cover' => [
        'max_file_size' => env('COVER_MAX_FILE_SIZE', 2048),
        'max_dimensions' => env('COVER_MAX_DIMENSIONS', 2000),
        'allowed_types' => array_map('trim', explode(',', env('COVER_ALLOWED_TYPES', 'jpg,jpeg,png,webp'))),
    ],
    
    'conversions' => [
        'thumb' => [
            'width' => env('MEDIA_THUMB_SIZE', 150),
            'height' => env('MEDIA_THUMB_SIZE', 150),
        ],
        'medium' => [
            'width' => env('MEDIA_MEDIUM_SIZE', 800),
            'height' => env('MEDIA_MEDIUM_SIZE', 800),
        ],
        'large' => [
            'width' => env('MEDIA_LARGE_SIZE', 1600),
            'height' => env('MEDIA_LARGE_SIZE', 1600),
        ],
    ],
    
    'use_random_names' => env('MEDIA_USE_RANDOM_NAMES', true),
];
