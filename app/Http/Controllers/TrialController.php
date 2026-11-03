<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrialController extends Controller
{
    /**
     * Display free trials with countdown and quick cancellation actions.
     */
    public function index(): View
    {
        $user = Auth::user();

        $trials = $user->subscriptions()
            ->where('status', 'Free Trial')
            ->orderBy('next_billing_date', 'asc')
            ->get();

        $today = Carbon::today();

        // Categorize trials
        $urgentTrials = $trials->filter(fn ($t) => $t->days_until_due <= 3);
        $upcomingTrials = $trials->filter(fn ($t) => $t->days_until_due > 3 && $t->days_until_due <= 7);
        $safeTrials = $trials->filter(fn ($t) => $t->days_until_due > 7);

        // Potential upcoming charge if not cancelled
        $potentialCharge = $trials->sum('cost');

        return view('trials.index', compact('trials', 'urgentTrials', 'upcomingTrials', 'safeTrials', 'potentialCharge'));
    }

    /**
     * Quickly cancel a trial so user is never charged.
     */
    public function cancel(Subscription $subscription): RedirectResponse
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription->update(['status' => 'Cancelled']);

        return back()->with('info', "Trial for \"{$subscription->name}\" cancelled. You won't be charged!");
    }

    /**
     * Convert trial to active subscription.
     */
    public function activate(Subscription $subscription): RedirectResponse
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $subscription->update(['status' => 'Active']);

        return back()->with('success', "Trial for \"{$subscription->name}\" converted to Active subscription!");
    }
}
