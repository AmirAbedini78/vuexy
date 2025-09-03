<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== بررسی Authentication و User Role ===\n\n";

// بررسی کاربران admin
echo "بررسی کاربران admin:\n";
$adminUsers = User::where('role', 'admin')->get();
echo "تعداد کاربران admin: " . $adminUsers->count() . "\n";

if ($adminUsers->count() > 0) {
    foreach ($adminUsers as $user) {
        echo "- ID: " . $user->id . 
             ", Name: " . $user->name . 
             ", Email: " . $user->email . 
             ", Role: " . $user->role . "\n";
    }
} else {
    echo "هیچ کاربر admin یافت نشد!\n";
}

echo "\n";

// بررسی تمام کاربران
echo "بررسی تمام کاربران:\n";
$allUsers = User::all();
echo "تعداد کل کاربران: " . $allUsers->count() . "\n";

if ($allUsers->count() > 0) {
    foreach ($allUsers as $user) {
        echo "- ID: " . $user->id . 
             ", Name: " . $user->name . 
             ", Email: " . $user->email . 
             ", Role: " . $user->role . "\n";
    }
} else {
    echo "هیچ کاربری یافت نشد!\n";
}

echo "\n=== بررسی جداول Providers ===\n";

// بررسی individual_users
$individualCount = DB::table('individual_users')->count();
echo "تعداد Individual Users: " . $individualCount . "\n";

if ($individualCount > 0) {
    $individualUsers = DB::table('individual_users')->orderBy('created_at', 'desc')->take(3)->get();
    foreach ($individualUsers as $user) {
        echo "- ID: " . $user->id . 
             ", Name: " . ($user->full_name ?? 'NULL') . 
             ", Created: " . $user->created_at . "\n";
    }
}

echo "\n";

// بررسی company_users
$companyCount = DB::table('company_users')->count();
echo "تعداد Company Users: " . $companyCount . "\n";

if ($companyCount > 0) {
    $companyUsers = DB::table('company_users')->orderBy('created_at', 'desc')->take(3)->get();
    foreach ($companyUsers as $user) {
        echo "- ID: " . $user->id . 
             ", Name: " . ($user->company_name ?? 'NULL') . 
             ", Created: " . $user->created_at . "\n";
    }
}

echo "\n=== راهنمای حل مشکل ===\n";
echo "1. اگر هیچ کاربر admin یافت نشد، یک کاربر admin ایجاد کنید\n";
echo "2. اگر داده‌های providers وجود دارد اما نمایش داده نمی‌شود، مشکل در authentication است\n";
echo "3. اگر هیچ provider یافت نشد، مشکل در ثبت‌نام است\n";
echo "4. برای تست موقت، از route /api/test/providers استفاده کنید\n";

echo "\n=== پایان بررسی ===\n";

