<?php

$token = 'awvuRvxdCEDrpMKAag12KA70KvimSd9lA1uzcwpBiOnRrEWlfP4icCj4wxlIkA4U';
$url = "http://localhost:8000/api/verify/{$token}";

echo "Testing API URL: {$url}\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Status Code: {$httpCode}\n";
echo "Response: {$response}\n";
if ($error) {
    echo "cURL Error: {$error}\n";
}
