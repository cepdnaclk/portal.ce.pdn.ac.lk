<?php

return [
  'image' => [
    'disk' => env('PROFILE_IMAGE_DISK', 'public'),
    'storage_path' => 'profile-images',
    'max_file_size' => 10240,
    'min_width' => 200,
    'min_height' => 200,
    'max_dimension' => 800,
    'quality' => 90,
    'cache_ttl' => 31536000,
  ],
];
