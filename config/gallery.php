<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gallery Feature Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the gallery feature is enabled for News
    | and Events. When disabled, gallery functionality will be hidden.
    |
    */

    'enabled' => env('GALLERY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The disk where gallery images will be stored. This should be one of
    | the disks configured in config/filesystems.php (e.g., 'public', 's3').
    |
    */

    'disk' => env('GALLERY_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Maximum Images Per Item
    |--------------------------------------------------------------------------
    |
    | The maximum number of images allowed in a gallery for each News or
    | Event item. This helps prevent abuse and manage storage.
    |
    */

    'max_images' => env('GALLERY_MAX_IMAGES', 12),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size for uploaded images in kilobytes.
    | Default is 10 MB (10240 KB).
    |
    */

    'max_file_size' => env('GALLERY_MAX_FILE_SIZE', 10240),

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | Array of allowed MIME types for gallery image uploads.
    | For security, only JPEG images are allowed by default.
    |
    */

    'allowed_mimes' => [
        'image/jpeg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Minimum Image Dimensions
    |--------------------------------------------------------------------------
    |
    | Minimum width and height in pixels for uploaded images.
    | Images smaller than these dimensions will be rejected.
    |
    */

    'min_width' => 200,
    'min_height' => 200,

    /*
    |--------------------------------------------------------------------------
    | Image Sizes
    |--------------------------------------------------------------------------
    |
    | Define different image sizes to be generated when an image is uploaded.
    | Each size includes width, height, and whether to maintain aspect ratio.
    |
    */

    'sizes' => [
        'thumb' => [
            'width' => 400,
            'height' => 400,
            'aspect_ratio' => true,
        ],
        'medium' => [
            'width' => 800,
            'height' => 800,
            'aspect_ratio' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Processing
    |--------------------------------------------------------------------------
    |
    | Whether to queue image processing jobs. When true, image resizing
    | will be processed asynchronously via Laravel queues.
    |
    */

    'queue_processing' => env('GALLERY_QUEUE_PROCESSING', false),

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | Base path within the storage disk where gallery images will be stored.
    |
    */

    'storage_path' => 'gallery',

    /*
    |--------------------------------------------------------------------------
    | Cache Control
    |--------------------------------------------------------------------------
    |
    | Cache-Control header value for gallery images in seconds.
    | Default is 1 year (31536000 seconds).
    |
    */

    'cache_ttl' => 31536000,

];
