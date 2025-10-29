<?php

// config/cors.php (create if not exists)
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'admin/login'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://vpa-booking-system-r0df.onrender.com'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];

