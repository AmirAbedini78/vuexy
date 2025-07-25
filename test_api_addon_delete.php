<?php
// تست حذف SpecialAddon
$url = 'http://localhost:8000/api/listings/1/special-addons/1';
$options = [
    'http' => [
        'method'  => 'DELETE',
    ],
];
$context  = stream_context_create($options);
echo file_get_contents($url, false, $context); 
