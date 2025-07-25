<?php
// تست ایجاد Itinerary جدید
$url = 'http://localhost:8000/api/listings/1/itineraries';
$data = [
    'day_number' => 1,
    'title' => 'Day 1 Title',
    'accommodation' => 'Hotel Test',
    'location' => 'Test City',
    'description' => 'Test Description',
    'link' => 'http://example.com',
    'order' => 1,
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
echo $result; 
