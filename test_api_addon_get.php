<?php
// تست دریافت لیست SpecialAddonها
$url = 'http://localhost:8000/api/listings/1/special-addons';
echo file_get_contents($url); 
