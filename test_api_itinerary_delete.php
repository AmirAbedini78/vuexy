<?php
// تست حذف Itinerary
$url = 'http://localhost:8000/api/listings/1/itineraries/1';
$options = [
    'http' => [
        'method'  => 'DELETE',
    ],
];
$context  = stream_context_create($options);
echo file_get_contents($url, false, $context); 
