<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 10240), // KB
    'allowed_file_types' => env('UPLOAD_ALLOWED_FILE_TYPES', 'pdf,doc,docx,xls,xlsx,txt'),
    'path' => env('UPLOAD_PATH', 'uploads/files'),

    /*
    |--------------------------------------------------------------------------
    | Image Upload Settings
    |--------------------------------------------------------------------------
    */
    'max_image_size' => env('UPLOAD_MAX_IMAGE_SIZE', 5120), // KB
    'allowed_image_types' => env('UPLOAD_ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif,webp'),
    'images_path' => env('UPLOAD_IMAGES_PATH', 'uploads/images'),
    
    // Image dimensions
    'max_image_width' => env('UPLOAD_MAX_IMAGE_WIDTH', 2048),
    'max_image_height' => env('UPLOAD_MAX_IMAGE_HEIGHT', 2048),
    'min_image_width' => env('UPLOAD_MIN_IMAGE_WIDTH', 100),
    'min_image_height' => env('UPLOAD_MIN_IMAGE_HEIGHT', 100),
];
