<?php

// Test the registration endpoint
$url = 'http://vuexy.test/api/auth/register';
$data = [
    'name' => 'Test User 5',
    'email' => 'test5@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Registration Response:\n";
echo $result . "\n\n";

// Test the login endpoint
$url = 'http://vuexy.test/api/auth/login';
$data = [
    'email' => 'test5@example.com',
    'password' => 'password123'
];

$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Login Response:\n";
echo $result . "\n";
