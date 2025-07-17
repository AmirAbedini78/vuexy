<?php

echo "Testing final verification with new token...\n\n";

$token = 'huUOMQCgxKJRtDGfGg7VuvEwVUDTAg5MWl70qbZu5YN7VmrZy0Se69nohjGp6R8C';
$url = "http://localhost:8000/api/verify/{$token}";

echo "Testing: {$url}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: {$response}\n";

if ($httpCode === 200) {
    echo "\n🎉 SUCCESS! Email verification is working!\n";
} else {
    echo "\n❌ Still having issues...\n";
} 
