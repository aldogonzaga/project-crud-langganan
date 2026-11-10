<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

echo "=============================================\n";
echo " TESTING FULL HTTP AUTH & USER WORKFLOWS\n";
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

// 1. HTTP GET /login renders successfully
$test("GET /login renders HTTP 200 with Sign In page", function() use ($kernel) {
    $request = Request::create('/login', 'GET');
    $response = $kernel->handle($request);
    if ($response->getStatusCode() !== 200) {
        throw new Exception("Expected 200, got: " . $response->getStatusCode());
    }
    if (!str_contains($response->getContent(), 'Sign In') && !str_contains($response->getContent(), 'Welcome back')) {
        throw new Exception("Login page missing 'Welcome back' / 'Sign In'");
    }
});

// 2. HTTP GET /register renders successfully
$test("GET /register renders HTTP 200 with Create Account page", function() use ($kernel) {
    $request = Request::create('/register', 'GET');
    $response = $kernel->handle($request);
    if ($response->getStatusCode() !== 200) {
        throw new Exception("Expected 200, got: " . $response->getStatusCode());
    }
    if (!str_contains($response->getContent(), 'Create your account')) {
        throw new Exception("Register page missing 'Create your account'");
    }
});

// 3. HTTP GET /demo-login creates session and redirects to dashboard
$test("GET /demo-login logs in demo user and redirects to /dashboard", function() use ($kernel) {
    $request = Request::create('/demo-login', 'GET');
    $response = $kernel->handle($request);
    if ($response->getStatusCode() !== 302) {
        throw new Exception("Expected 302 redirect, got: " . $response->getStatusCode());
    }
    $targetUrl = $response->headers->get('Location');
    if (!str_contains($targetUrl, 'dashboard')) {
        throw new Exception("Expected redirect to dashboard, got: $targetUrl");
    }
});

// 4. Controller Test: Successful Login
$test("AuthController::login with valid credentials authenticates user", function() {
    $request = Request::create('/login', 'POST', [
        'email' => 'demo@example.com',
        'password' => 'password123',
    ]);
    // Start session
    $request->setLaravelSession(app('session.store'));

    $controller = app(App\Http\Controllers\AuthController::class);
    $response = $controller->login($request);

    if ($response->getStatusCode() !== 302) {
        throw new Exception("Expected 302 redirect after login, got: " . $response->getStatusCode());
    }
    if (!Auth::check()) {
        throw new Exception("Auth::check() is false after login!");
    }
    if (Auth::user()->email !== 'demo@example.com') {
        throw new Exception("Logged in user is not demo@example.com");
    }

    Auth::logout();
});

// 5. Controller Test: Failed Login with Bad Password
$test("AuthController::login with invalid password rejects and redirects back", function() {
    $request = Request::create('/login', 'POST', [
        'email' => 'demo@example.com',
        'password' => 'wrongPassword999',
    ]);
    $request->setLaravelSession(app('session.store'));

    $controller = app(App\Http\Controllers\AuthController::class);
    $response = $controller->login($request);

    if ($response->getStatusCode() !== 302) {
        throw new Exception("Expected 302 redirect back, got: " . $response->getStatusCode());
    }
    if (Auth::check()) {
        throw new Exception("Auth::check() is true after bad password!");
    }
});

// 6. Controller Test: User Registration
$test("AuthController::register creates user and auto-logs in", function() {
    $tempEmail = 'http_reg_' . time() . '@example.com';
    $request = Request::create('/register', 'POST', [
        'name' => 'Sara Connor',
        'email' => $tempEmail,
        'currency' => '$',
        'password' => 'terminator123',
        'password_confirmation' => 'terminator123',
    ]);
    $request->setLaravelSession(app('session.store'));

    $controller = app(App\Http\Controllers\AuthController::class);
    $response = $controller->register($request);

    if ($response->getStatusCode() !== 302) {
        throw new Exception("Expected 302 redirect after register");
    }
    if (!Auth::check()) {
        throw new Exception("Auth::check() failed after registration");
    }
    if (Auth::user()->email !== $tempEmail) {
        throw new Exception("Registered user email mismatch");
    }

    // Cleanup
    $user = Auth::user();
    Auth::logout();
    $user->delete();
});

// 7. Controller Test: Profile Update
$test("ProfileController::update saves name, email, currency & budget", function() {
    $demo = User::where('email', 'demo@example.com')->first();
    Auth::login($demo);

    $request = Request::create('/profile', 'PUT', [
        'name' => 'Alex Morgan Pro',
        'email' => 'demo@example.com',
        'currency' => 'Rp',
        'monthly_budget' => 500000.00,
    ]);
    $request->setLaravelSession(app('session.store'));

    $controller = app(App\Http\Controllers\ProfileController::class);
    $response = $controller->update($request);

    $demo->refresh();
    if ($demo->name !== 'Alex Morgan Pro' || $demo->currency !== 'Rp' || (float)$demo->monthly_budget !== 500000.00) {
        throw new Exception("Profile attributes were not updated properly");
    }

    // Reset back
    $demo->update([
        'name' => 'Alex Morgan',
        'currency' => '$',
        'monthly_budget' => 200.00,
    ]);
    Auth::logout();
});

echo "\n=============================================\n";
$passed = count(array_filter($tests));
$total = count($tests);
echo "HTTP AUTH TEST RESULT: $passed / $total PASSED\n";
echo "=============================================\n";

if ($passed !== $total) exit(1);

