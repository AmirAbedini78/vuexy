<?php

echo "Testing verify route specifically...\n\n";

$token = 'awvuRvxdCEDrpMKAag12KA70KvimSd9lA1uzcwpBiOnRrEWlfP4icCj4wxlIkA4U';

// Test 1: With Accept header
$url1 = "http://localhost:8000/api/verify/{$token}";
echo "Test 1: {$url1} (with Accept: application/json)\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: " . substr($response, 0, 200) . "...\n\n";

// Test 2: Without Accept header
$url2 = "http://localhost:8000/api/verify/{$token}";
echo "Test 2: {$url2} (without Accept header)\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: " . substr($response, 0, 200) . "...\n\n"; 
