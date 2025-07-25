<?php

$url = 'http://localhost:8000/api/listings';
$data = [
    'user_id' => 1, // مقدار user_id تستی
    'listing_type' => 'single-date',
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) {
    echo "API call failed!\n";
} else {
    echo "API response:\n";
    echo $result;
} 
