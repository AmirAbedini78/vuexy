<?php
// تست دریافت لیست Itineraryها
$url = 'http://localhost:8000/api/listings/1/itineraries';
echo file_get_contents($url); 
