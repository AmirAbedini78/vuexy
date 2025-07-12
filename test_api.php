<?php

// Simple test to check if routes are working
echo "Testing API routes...\n";

// Test if we can access the routes
$baseUrl = 'http://vuexy.test/api';

// Test individual users route
$individualUrl = $baseUrl . '/individual-users';
echo "Testing: $individualUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $individualUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['test' => 'data']));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Individual Users - HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test company users route
$companyUrl = $baseUrl . '/company-users';
echo "Testing: $companyUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $companyUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['test' => 'data']));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Company Users - HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

echo "Test completed.\n";
