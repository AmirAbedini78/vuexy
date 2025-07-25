<?php
// تست ویرایش SpecialAddon
$url = 'http://localhost:8000/api/listings/1/special-addons/1';
$data = [
    'title' => 'Updated Addon 1',
    'price' => 250,
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
