<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'verify/*', 'auth/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('FRONTEND_URL', 'https://portal.explorerelite.com')],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];