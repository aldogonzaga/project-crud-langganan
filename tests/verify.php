<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== RUNNING VERIFICATION SUITE ===\n\n";

$passCount = 0;
$failCount = 0;

$it = function (string $description, callable $fn) use (&$passCount, &$failCount) {
    try {
        $fn();
        echo " [PASS] $description\n";
        $passCount++;
    } catch (\Throwable $e) {
        echo " [FAIL] $description\n";
        echo "        Error: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
        $failCount++;
    }
};

// 1. User Model & Currency Formatter
$it('User model formats currencies properly', function () {
    $userUsd = new User(['currency' => '$']);
    if ($userUsd->formatCurrency(19.99) !== '$19.99') {
        throw new Exception("Expected $19.99, got " . $userUsd->formatCurrency(19.99));
    }

    $userIdr = new User(['currency' => 'Rp']);
    if ($userIdr->formatCurrency(150000) !== 'Rp 150.000') {
        throw new Exception("Expected Rp 150.000, got " . $userIdr->formatCurrency(150000));
    }
});

// 2. Demo User Exists with Preloaded Subscriptions
$it('Demo user exists with seeded subscriptions', function () {
    $demo = User::where('email', 'demo@example.com')->first();
    if (!$demo) {
        throw new Exception("Demo user not found!");
    }

    $count = $demo->subscriptions()->count();
    if ($count === 0) {
        throw new Exception("Demo user has 0 subscriptions!");
    }
});

// 3. Normalized Monthly Cost Calculation
$it('Calculates correct monthly costs for Weekly, Monthly, and Yearly cycles', function () {
    $monthly = new Subscription(['cost' => 12.00, 'billing_cycle' => 'Monthly']);
    if ($monthly->monthly_cost != 12.00) throw new Exception("Monthly cost incorrect");

    $yearly = new Subscription(['cost' => 120.00, 'billing_cycle' => 'Yearly']);
    if ($yearly->monthly_cost != 10.00) throw new Exception("Yearly cost incorrect");

    $weekly = new Subscription(['cost' => 10.00, 'billing_cycle' => 'Weekly']);
    $expectedWeekly = round((10.00 * 52) / 12, 2);
    if ($weekly->monthly_cost != $expectedWeekly) throw new Exception("Weekly cost incorrect");
});

// 4. Date Logic & Urgency Highlighting (Critical <= 3 days, Warning 4-7 days)
$it('Applies date urgency rules: <= 3 days is critical, 4-7 days is warning', function () {
    $sub3Days = new Subscription([
        'status' => 'Active',
        'next_billing_date' => Carbon::today()->addDays(2),
    ]);
    if ($sub3Days->urgency_level !== 'critical') {
        throw new Exception("Expected 'critical' for 2 days, got: " . $sub3Days->urgency_level);
    }

    $sub6Days = new Subscription([
        'status' => 'Active',
        'next_billing_date' => Carbon::today()->addDays(6),
    ]);
    if ($sub6Days->urgency_level !== 'warning') {
        throw new Exception("Expected 'warning' for 6 days, got: " . $sub6Days->urgency_level);
    }

    $sub20Days = new Subscription([
        'status' => 'Active',
        'next_billing_date' => Carbon::today()->addDays(20),
    ]);
    if ($sub20Days->urgency_level !== 'normal') {
        throw new Exception("Expected 'normal' for 20 days, got: " . $sub20Days->urgency_level);
    }

    $subCancelled = new Subscription([
        'status' => 'Cancelled',
        'next_billing_date' => Carbon::today()->addDays(1),
    ]);
    if ($subCancelled->urgency_level !== 'inactive') {
        throw new Exception("Expected 'inactive' for cancelled, got: " . $subCancelled->urgency_level);
    }
});

// 5. CRUD: Create, Read, Update, Soft Delete & Restore
$it('Performs full CRUD with soft deletes', function () {
    $user = User::firstOrCreate(
        ['email' => 'crud_test@example.com'],
        ['name' => 'CRUD Tester', 'password' => Hash::make('password'), 'currency' => '$']
    );

    // Create
    $sub = $user->subscriptions()->create([
        'name' => 'Figma Pro',
        'cost' => 12.00,
        'billing_cycle' => 'Monthly',
        'next_billing_date' => Carbon::today()->addDays(14)->toDateString(),
        'category' => 'Work',
        'status' => 'Active',
        'icon' => '📐',
        'notes' => 'Design tool',
    ]);

    if (!$sub->id) throw new Exception("Failed to create subscription");

    // Update
    $sub->update(['cost' => 15.00, 'status' => 'Free Trial']);
    $sub->refresh();
    if ($sub->cost != 15.00 || $sub->status !== 'Free Trial') {
        throw new Exception("Failed to update subscription");
    }

    // Soft Delete
    $sub->delete();
    if (!$sub->trashed()) throw new Exception("Subscription was not soft-deleted");

    // Verify it is not in active list
    $activeCount = $user->subscriptions()->where('id', $sub->id)->count();
    if ($activeCount !== 0) throw new Exception("Soft deleted sub still appears in normal queries");

    // Restore
    $sub->restore();
    if ($sub->trashed()) throw new Exception("Subscription failed to restore");

    // Cleanup
    $sub->forceDelete();
});

// 6. Category Breakdown and Filter Scope
$it('Properly filters by category and status', function () {
    $demo = User::where('email', 'demo@example.com')->first();
    $entertainmentSubs = $demo->subscriptions()->filter(['category' => 'Entertainment'])->get();
    foreach ($entertainmentSubs as $s) {
        if ($s->category !== 'Entertainment') {
            throw new Exception("Filter returned non-entertainment subscription");
        }
    }

    $activeSubs = $demo->subscriptions()->filter(['status' => 'Active'])->get();
    foreach ($activeSubs as $s) {
        if ($s->status !== 'Active') {
            throw new Exception("Filter returned non-active subscription");
        }
    }
});

// 7. Route and View Rendering Test
$it('Renders Dashboard, Subscriptions, Trials, Analytics, Profile without error', function () {
    view()->share('errors', new \Illuminate\Support\ViewErrorBag());
    $demo = User::where('email', 'demo@example.com')->first();
    Auth::login($demo);

    $dashboardView = app(App\Http\Controllers\DashboardController::class)->index();
    if (!$dashboardView->render()) throw new Exception("Failed to render dashboard view");

    $request = new Illuminate\Http\Request();
    $subsView = app(App\Http\Controllers\SubscriptionController::class)->index($request);
    if (!$subsView->render()) throw new Exception("Failed to render subscriptions view");

    $trialsView = app(App\Http\Controllers\TrialController::class)->index();
    if (!$trialsView->render()) throw new Exception("Failed to render trials view");

    $analyticsView = app(App\Http\Controllers\AnalyticsController::class)->index();
    if (!$analyticsView->render()) throw new Exception("Failed to render analytics view");

    $profileView = app(App\Http\Controllers\ProfileController::class)->edit();
    if (!$profileView->render()) throw new Exception("Failed to render profile view");
});

// 8. Route Dispatching Test
$it('Dispatches home and login routes correctly', function () {
    $guestRoute = app('router')->getRoutes()->match(Illuminate\Http\Request::create('/login', 'GET'));
    if ($guestRoute->getName() !== 'login') {
        throw new Exception("Expected route 'login', got: " . $guestRoute->getName());
    }

    $dashRoute = app('router')->getRoutes()->match(Illuminate\Http\Request::create('/dashboard', 'GET'));
    if ($dashRoute->getName() !== 'dashboard') {
        throw new Exception("Expected route 'dashboard', got: " . $dashRoute->getName());
    }
});

echo "\n=================================\n";
echo "RESULT: $passCount PASSED, $failCount FAILED\n";
echo "=================================\n";

if ($failCount > 0) exit(1);
