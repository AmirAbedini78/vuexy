<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\IndividualUser;
use App\Models\CompanyUser;
use App\Models\Listing;
use Illuminate\Support\Facades\DB;

echo "=== Testing Admin API Endpoints ===\n\n";

// Test 1: Check if admin users exist
echo "1. Checking admin users:\n";
$adminUsers = User::where('role', 'admin')->get();
echo "   Found " . $adminUsers->count() . " admin users\n";
if ($adminUsers->count() > 0) {
    foreach ($adminUsers as $user) {
        echo "   - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
    }
} else {
    echo "   WARNING: No admin users found!\n";
}

echo "\n";

// Test 2: Check providers data
echo "2. Checking providers data:\n";
$individualCount = IndividualUser::count();
$companyCount = CompanyUser::count();
echo "   Individual Users: {$individualCount}\n";
echo "   Company Users: {$companyCount}\n";
echo "   Total Providers: " . ($individualCount + $companyCount) . "\n";

if ($individualCount > 0) {
    echo "   Sample Individual Users:\n";
    $individuals = IndividualUser::take(3)->get();
    foreach ($individuals as $user) {
        echo "   - ID: {$user->id}, Name: {$user->full_name}, Specialization: {$user->activity_specialization}\n";
    }
}

if ($companyCount > 0) {
    echo "   Sample Company Users:\n";
    $companies = CompanyUser::take(3)->get();
    foreach ($companies as $user) {
        echo "   - ID: {$user->id}, Name: {$user->company_name}, Specialization: {$user->activity_specialization}\n";
    }
}

echo "\n";

// Test 3: Check listings data
echo "3. Checking listings data:\n";
$listingsCount = Listing::count();
echo "   Total Listings: {$listingsCount}\n";

if ($listingsCount > 0) {
    echo "   Sample Listings:\n";
    $listings = Listing::take(3)->get();
    foreach ($listings as $listing) {
        echo "   - ID: {$listing->id}, Title: {$listing->listing_title}, Created: {$listing->created_at}\n";
    }
}

echo "\n";

// Test 4: Simulate the providers query
echo "4. Testing providers query structure:\n";
try {
    $individualUsers = IndividualUser::select([
        'id',
        'full_name as provider_name',
        'activity_specialization',
        'years_of_experience',
        'country_of_operation',
        'want_to_be_listed',
        'created_at',
        DB::raw("'individual' as provider_type"),
        DB::raw("'Live' as status"),
        DB::raw("0 as total_listings"),
        DB::raw("0 as total_bookings")
    ])->get();

    $companyUsers = CompanyUser::select([
        'id',
        'company_name as provider_name',
        'activity_specialization',
        'business_type as years_of_experience',
        'country as country_of_operation',
        'want_to_be_listed',
        'created_at',
        DB::raw("'company' as provider_type"),
        DB::raw("'Live' as status"),
        DB::raw("0 as total_listings"),
        DB::raw("0 as total_bookings")
    ])->get();

    $providers = collect()->concat($individualUsers)->concat($companyUsers);
    
    echo "   Query successful!\n";
    echo "   Total providers from query: " . $providers->count() . "\n";
    
    if ($providers->count() > 0) {
        echo "   Sample provider data:\n";
        $sample = $providers->first();
        foreach ($sample->toArray() as $key => $value) {
            echo "   - {$key}: {$value}\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nRecommendations:\n";
if ($adminUsers->count() === 0) {
    echo "1. Create an admin user first\n";
}
if ($individualCount + $companyCount === 0) {
    echo "2. Register some providers (individual or company users)\n";
}
echo "3. Check the Laravel logs for any errors\n";
echo "4. Test the API endpoints with proper authentication\n";
