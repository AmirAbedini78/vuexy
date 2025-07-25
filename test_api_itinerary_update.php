<?php
// تست ویرایش Itinerary
$url = 'http://localhost:8000/api/listings/1/itineraries/1';
$data = [
    'title' => 'Updated Day 1 Title',
    'accommodation' => 'Updated Hotel',
];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'PUT',
        'content' => json_encode($data),
    ],
];
$context  = stream_context_create($options);
echo file_get_contents($url, false, $context); 
