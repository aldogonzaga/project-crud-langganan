<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display visual charts and spending breakdown.
     */
    public function index(): View
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()->get();
        $activeSubs = $subscriptions->where('status', 'Active');
        $cancelledSubs = $subscriptions->where('status', 'Cancelled');

        $totalMonthly = $activeSubs->sum(fn ($s) => $s->monthly_cost);
        $totalYearly = $totalMonthly * 12;

        // Categories distribution
        $categories = ['Entertainment', 'Utilities', 'Health', 'Work', 'Education', 'Food', 'Shopping', 'Others'];
        $categoryData = [];
        $categoryColors = [
            'Entertainment' => '#d95d85', // Berry Rose
            'Utilities'     => '#5c9fb8', // Soft Muted Blue
            'Health'        => '#73b38c', // Cozy Sage Green
            'Work'          => '#9c7bb8', // Soft Lavender
            'Food'          => '#e88d67', // Pastel Coral / Peach
            'Shopping'      => '#e3b15b', // Warm Amber / Honey
            'Education'     => '#4ea59f', // Soft Seafoam / Jade
            'Others'        => '#a89ba2', // Warm Dusty Mauve
        ];

        foreach ($categories as $cat) {
            $catSum = $activeSubs->where('category', $cat)->sum(fn ($s) => $s->monthly_cost);
            if ($catSum > 0) {
                $categoryData[$cat] = [
                    'amount' => round($catSum, 2),
                    'percentage' => $totalMonthly > 0 ? round(($catSum / $totalMonthly) * 100, 1) : 0,
                    'color' => $categoryColors[$cat] ?? '#94a3b8',
                ];
            }
        }

        // Billing Cycle Breakdown
        $cycleData = [
            'Monthly' => [
                'count' => $activeSubs->where('billing_cycle', 'Monthly')->count(),
                'total' => $activeSubs->where('billing_cycle', 'Monthly')->sum('cost'),
            ],
            'Yearly' => [
                'count' => $activeSubs->where('billing_cycle', 'Yearly')->count(),
                'total' => $activeSubs->where('billing_cycle', 'Yearly')->sum('cost'),
            ],
            'Weekly' => [
                'count' => $activeSubs->where('billing_cycle', 'Weekly')->count(),
                'total' => $activeSubs->where('billing_cycle', 'Weekly')->sum('cost'),
            ],
        ];

        // Top 5 Most Expensive Subscriptions
        $topExpenses = $activeSubs->sortByDesc('monthly_cost')->take(5);

        // Savings from Cancelled Subscriptions
        $monthlySavings = $cancelledSubs->sum(fn ($s) => $s->monthly_cost);
        $yearlySavings = $monthlySavings * 12;

        $averageMonthlyCost = $activeSubs->count() > 0 ? round($totalMonthly / $activeSubs->count(), 2) : 0;

        return view('analytics.index', compact(
            'user',
            'totalMonthly',
            'totalYearly',
            'categoryData',
            'cycleData',
            'topExpenses',
            'monthlySavings',
            'yearlySavings',
            'averageMonthlyCost',
            'activeSubs'
        ));
    }
}
