<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Creating Admin User ===\n\n";

// Check if admin user already exists
$existingAdmin = User::where('role', 'admin')->first();
if ($existingAdmin) {
    echo "Admin user already exists:\n";
    echo "ID: {$existingAdmin->id}\n";
    echo "Name: {$existingAdmin->name}\n";
    echo "Email: {$existingAdmin->email}\n";
    echo "Role: {$existingAdmin->role}\n";
    exit;
}

// Create admin user
try {
    $adminUser = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    echo "Admin user created successfully!\n";
    echo "ID: {$adminUser->id}\n";
    echo "Name: {$adminUser->name}\n";
    echo "Email: {$adminUser->email}\n";
    echo "Password: password123\n";
    echo "Role: {$adminUser->role}\n";
    echo "\nYou can now login to the admin dashboard with these credentials.\n";
    
} catch (Exception $e) {
    echo "Error creating admin user: " . $e->getMessage() . "\n";
}
