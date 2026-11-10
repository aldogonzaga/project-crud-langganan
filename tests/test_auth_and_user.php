<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "=============================================\n";
echo " TESTING LOGIN AND USER FUNCTIONS (SubTrack)\n";
echo "=============================================\n\n";

$tests = [];
$test = function (string $name, callable $fn) use (&$tests) {
    try {
        $fn();
        echo "  [PASS] $name\n";
        $tests[] = true;
    } catch (\Throwable $e) {
        echo "  [FAIL] $name\n";
        echo "         -> " . $e->getMessage() . " on line " . $e->getLine() . "\n";
        $tests[] = false;
    }
};

// 1. Password hashing and verification
$test("Password Hashing & Verification", function() {
    $password = 'secretPassword123';
    $hashed = Hash::make($password);
    if (!Hash::check($password, $hashed)) {
        throw new Exception("Hash::check failed for valid password");
    }
    if (Hash::check('wrongPassword', $hashed)) {
        throw new Exception("Hash::check succeeded for invalid password");
    }
});

// 2. Demo User Authentication
$test("Demo User Credentials and Auth::attempt", function() {
    $demo = User::where('email', 'demo@example.com')->first();
    if (!$demo) {
        throw new Exception("Demo user not found in database");
    }

    $attemptSuccess = Auth::attempt(['email' => 'demo@example.com', 'password' => 'password123']);
    if (!$attemptSuccess) {
        throw new Exception("Auth::attempt failed with correct demo credentials");
    }

    $attemptFail = Auth::attempt(['email' => 'demo@example.com', 'password' => 'incorrectPassword']);
    if ($attemptFail) {
        throw new Exception("Auth::attempt succeeded with incorrect credentials");
    }
});

// 3. User Registration Flow (Controller Logic)
$test("User Registration Flow", function() {
    $uniqueEmail = 'reg_test_' . time() . '@example.com';
    $user = User::create([
        'name' => 'Test Registrant',
        'email' => $uniqueEmail,
        'currency' => 'Rp',
        'monthly_budget' => 2500000.00,
        'password' => Hash::make('mypassword123'),
    ]);

    if (!$user->id) {
        throw new Exception("User creation failed");
    }

    $loggedIn = Auth::attempt(['email' => $uniqueEmail, 'password' => 'mypassword123']);
    if (!$loggedIn) {
        throw new Exception("Could not log in newly registered user");
    }

    if (Auth::user()->currency !== 'Rp') {
        throw new Exception("User currency preference not saved correctly");
    }

    // Clean up
    $user->delete();
});

// 4. User Profile Update (Name, Currency, Monthly Budget)
$test("Profile & Preferences Update", function() {
    $demo = User::where('email', 'demo@example.com')->first();
    $originalName = $demo->name;

    $demo->update([
        'name' => 'Alex M. Updated',
        'currency' => '€',
        'monthly_budget' => 350.00,
    ]);

    $demo->refresh();
    if ($demo->name !== 'Alex M. Updated') {
        throw new Exception("Name update failed");
    }
    if ($demo->currency !== '€') {
        throw new Exception("Currency update failed");
    }
    if ((float)$demo->monthly_budget !== 350.00) {
        throw new Exception("Monthly budget update failed");
    }

    // Revert back
    $demo->update([
        'name' => $originalName,
        'currency' => '$',
        'monthly_budget' => 200.00,
    ]);
});

// 5. Password Update
$test("Password Update Flow", function() {
    $user = User::create([
        'name' => 'Password Changer',
        'email' => 'pw_test_' . time() . '@example.com',
        'currency' => '$',
        'password' => Hash::make('oldPassword123'),
    ]);

    // Check old password
    if (!Hash::check('oldPassword123', $user->password)) {
        throw new Exception("Initial password check failed");
    }

    // Update password
    $user->update([
        'password' => Hash::make('newSuperPassword456'),
    ]);
    $user->refresh();

    if (!Hash::check('newSuperPassword456', $user->password)) {
        throw new Exception("New password check failed");
    }
    if (Hash::check('oldPassword123', $user->password)) {
        throw new Exception("Old password still active after change");
    }

    $user->delete();
});

// 6. User Subscription Isolation (Multi-tenant Privacy)
$test("User Data Isolation (User A cannot see or access User B subscriptions)", function() {
    $userA = User::create([
        'name' => 'User Alpha',
        'email' => 'alpha_' . time() . '@example.com',
        'currency' => '$',
        'password' => Hash::make('password'),
    ]);

    $userB = User::create([
        'name' => 'User Beta',
        'email' => 'beta_' . time() . '@example.com',
        'currency' => '$',
        'password' => Hash::make('password'),
    ]);

    $subA = $userA->subscriptions()->create([
        'name' => 'Alpha Private Service',
        'cost' => 19.99,
        'billing_cycle' => 'Monthly',
        'next_billing_date' => now()->addDays(10)->toDateString(),
        'category' => 'Work',
        'status' => 'Active',
    ]);

    $subB = $userB->subscriptions()->create([
        'name' => 'Beta Private Service',
        'cost' => 49.99,
        'billing_cycle' => 'Monthly',
        'next_billing_date' => now()->addDays(10)->toDateString(),
        'category' => 'Work',
        'status' => 'Active',
    ]);

    // User A query should only find subA
    $userASubs = $userA->subscriptions()->pluck('name')->toArray();
    if (!in_array('Alpha Private Service', $userASubs)) {
        throw new Exception("User A cannot see their own subscription");
    }
    if (in_array('Beta Private Service', $userASubs)) {
        throw new Exception("DATA LEAK: User A can see User B's subscription!");
    }

    // User B query should only find subB
    $userBSubs = $userB->subscriptions()->pluck('name')->toArray();
    if (!in_array('Beta Private Service', $userBSubs)) {
        throw new Exception("User B cannot see their own subscription");
    }
    if (in_array('Alpha Private Service', $userBSubs)) {
        throw new Exception("DATA LEAK: User B can see User A's subscription!");
    }

    // Cleanup
    $subA->forceDelete();
    $subB->forceDelete();
    $userA->delete();
    $userB->delete();
});

// 7. Currency Formatter by User
$test("User Currency Formatter Method", function() {
    $userDollar = new User(['currency' => '$']);
    if ($userDollar->formatCurrency(24.5) !== '$24.50') {
        throw new Exception("Expected $24.50, got " . $userDollar->formatCurrency(24.5));
    }

    $userRupiah = new User(['currency' => 'Rp']);
    if ($userRupiah->formatCurrency(185000) !== 'Rp 185.000') {
        throw new Exception("Expected Rp 185.000, got " . $userRupiah->formatCurrency(185000));
    }

    $userEuro = new User(['currency' => '€']);
    if ($userEuro->formatCurrency(9.99) !== '€9.99') {
        throw new Exception("Expected €9.99, got " . $userEuro->formatCurrency(9.99));
    }
});

echo "\n=============================================\n";
$passed = count(array_filter($tests));
$total = count($tests);
echo "SUMMARY: $passed / $total TESTS PASSED\n";
echo "=============================================\n";

if ($passed !== $total) exit(1);
