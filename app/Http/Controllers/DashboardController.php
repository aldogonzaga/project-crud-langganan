<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main analytics & dashboard overview.
     */
    public function index(): View
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()->get();

        $activeSubscriptions = $subscriptions->where('status', 'Active');
        $freeTrials = $subscriptions->where('status', 'Free Trial');
        $cancelledSubscriptions = $subscriptions->where('status', 'Cancelled');

        // Total Monthly Spend calculated from active subscriptions
        $totalMonthlySpend = $activeSubscriptions->sum(fn ($sub) => $sub->monthly_cost);
        $totalYearlySpend = $totalMonthlySpend * 12;

        // Payments due in next 7 days
        $today = Carbon::today();
        $in7Days = Carbon::today()->addDays(7);
        $in3Days = Carbon::today()->addDays(3);

        $upcomingPayments = $subscriptions
            ->where('status', '!=', 'Cancelled')
            ->filter(function ($sub) use ($today, $in7Days) {
                if (!$sub->next_billing_date) return false;
                $date = Carbon::parse($sub->next_billing_date)->startOfDay();
                return $date->betweenIncluded($today, $in7Days) || $date->lt($today);
            })
            ->sortBy('next_billing_date')
            ->values();

        // Critical payments count (due today or within 3 days or overdue)
        $criticalPaymentsCount = $subscriptions
            ->where('status', '!=', 'Cancelled')
            ->filter(function ($sub) use ($today, $in3Days) {
                if (!$sub->next_billing_date) return false;
                $date = Carbon::parse($sub->next_billing_date)->startOfDay();
                return $date->lte($in3Days);
            })
            ->count();

        // Active free trials ending soon (next 14 days or overdue)
        $trialsEndingSoon = $freeTrials
            ->sortBy('next_billing_date')
            ->values();

        // Category breakdown for charts
        $categoryBreakdown = [];
        $categories = ['Entertainment', 'Utilities', 'Health', 'Work', 'Education', 'Food', 'Shopping', 'Others'];

        foreach ($categories as $cat) {
            $catSubs = $activeSubscriptions->where('category', $cat);
            $catTotal = $catSubs->sum(fn ($sub) => $sub->monthly_cost);
            if ($catTotal > 0 || $activeSubscriptions->isEmpty()) {
                $categoryBreakdown[$cat] = round($catTotal, 2);
            }
        }

        // Cycle breakdown
        $cycleBreakdown = [
            'Monthly' => $activeSubscriptions->where('billing_cycle', 'Monthly')->count(),
            'Yearly' => $activeSubscriptions->where('billing_cycle', 'Yearly')->count(),
            'Weekly' => $activeSubscriptions->where('billing_cycle', 'Weekly')->count(),
        ];

        // Recent 5 subscriptions
        $recentSubscriptions = $subscriptions->sortByDesc('created_at')->take(5);

        return view('dashboard.index', compact(
            'user',
            'subscriptions',
            'activeSubscriptions',
            'freeTrials',
            'cancelledSubscriptions',
            'totalMonthlySpend',
            'totalYearlySpend',
            'upcomingPayments',
            'criticalPaymentsCount',
            'trialsEndingSoon',
            'categoryBreakdown',
            'cycleBreakdown',
            'recentSubscriptions'
        ));
    }
}
