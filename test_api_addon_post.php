<?php
// تست ایجاد SpecialAddon جدید
$url = 'http://localhost:8000/api/listings/1/special-addons';
$data = [
    'number' => "1",
    'title' => 'Addon 1',
    'description' => 'Addon Description',
    'price' => 200,
    'is_active' => true,
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
echo file_get_contents($url, false, $context); 
