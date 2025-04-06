<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    // 'allowed_origins' => ['*'], // Change this to restrict specific origins
    'allowed_origins' => ['http://localhost:3000, https://movies-admin-panel-hwc9.vercel.app'], // Replace with your React app's origin
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // default value is false. Set to true if you're using authentication like Laravel Sanctum.
];
