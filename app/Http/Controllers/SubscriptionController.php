<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions with filtering & sorting.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filters = [
            'search' => $request->input('search'),
            'category' => $request->input('category', 'All'),
            'status' => $request->input('status', 'All'),
            'cycle' => $request->input('cycle', 'All'),
            'sort' => $request->input('sort', 'due_soon'),
            'view' => $request->input('view', 'cards'), // 'cards' or 'table'
        ];

        $query = $user->subscriptions()->filter($filters);
        $subscriptions = $query->paginate(12)->withQueryString();

        // Calculate summary for currently filtered records
        $allUserSubs = $user->subscriptions()->get();
        $totalCount = $allUserSubs->count();
        $activeCount = $allUserSubs->where('status', 'Active')->count();
        $trialCount = $allUserSubs->where('status', 'Free Trial')->count();
        $cancelledCount = $allUserSubs->where('status', 'Cancelled')->count();
        $trashCount = $user->subscriptions()->onlyTrashed()->count();

        $categories = ['Entertainment', 'Utilities', 'Health', 'Work', 'Education', 'Food', 'Shopping', 'Others'];

        return view('subscriptions.index', compact(
            'subscriptions',
            'filters',
            'categories',
            'totalCount',
            'activeCount',
            'trialCount',
            'cancelledCount',
            'trashCount'
        ));
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create(Request $request): View
    {
        $categories = ['Entertainment', 'Utilities', 'Health', 'Work', 'Education', 'Food', 'Shopping', 'Others'];
        $cycles = ['Monthly', 'Yearly', 'Weekly'];
        $statuses = ['Active', 'Free Trial', 'Cancelled'];

        $cloneFrom = null;
        if ($request->has('clone_id')) {
            $cloneFrom = Auth::user()->subscriptions()->find($request->input('clone_id'));
        }

        return view('subscriptions.create', compact('categories', 'cycles', 'statuses', 'cloneFrom'));
    }

    /**
     * Store a newly created subscription in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'cost' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:Monthly,Yearly,Weekly'],
            'next_billing_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:Active,Free Trial,Cancelled'],
            'icon' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $subscription = Auth::user()->subscriptions()->create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Subscription \"{$subscription->name}\" added successfully!",
                'subscription' => $subscription,
            ]);
        }

        return redirect()->back()
            ->with('success', "Subscription \"{$subscription->name}\" added successfully!");
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription): View
    {
        $this->authorizeSubscription($subscription);

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(Subscription $subscription): View
    {
        $this->authorizeSubscription($subscription);

        $categories = ['Entertainment', 'Utilities', 'Health', 'Work', 'Education', 'Food', 'Shopping', 'Others'];
        $cycles = ['Monthly', 'Yearly', 'Weekly'];
        $statuses = ['Active', 'Free Trial', 'Cancelled'];

        return view('subscriptions.edit', compact('subscription', 'categories', 'cycles', 'statuses'));
    }

    /**
     * Update the specified subscription in storage.
     */
    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $this->authorizeSubscription($subscription);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'cost' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', 'in:Monthly,Yearly,Weekly'],
            'next_billing_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:Active,Free Trial,Cancelled'],
            'icon' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $subscription->update($validated);

        return redirect()->route('subscriptions.index')
            ->with('success', "Subscription \"{$subscription->name}\" updated successfully!");
    }

    /**
     * Quick status update (e.g. Cancel or Activate).
     */
    public function quickStatus(Request $request, Subscription $subscription): RedirectResponse
    {
        $this->authorizeSubscription($subscription);

        $validated = $request->validate([
            'status' => ['required', 'in:Active,Free Trial,Cancelled'],
        ]);

        $subscription->update(['status' => $validated['status']]);

        $msg = match ($validated['status']) {
            'Cancelled' => "Subscription \"{$subscription->name}\" has been marked as Cancelled.",
            'Active' => "Subscription \"{$subscription->name}\" is now Active!",
            'Free Trial' => "Subscription \"{$subscription->name}\" status set to Free Trial.",
        };

        return back()->with('info', $msg);
    }

    /**
     * Soft delete the specified subscription.
     */
    public function destroy(Subscription $subscription): RedirectResponse
    {
        $this->authorizeSubscription($subscription);

        $name = $subscription->name;
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', "Subscription \"{$name}\" moved to Trash.");
    }

    /**
     * List trashed (soft-deleted) subscriptions.
     */
    public function trash(): View
    {
        $user = Auth::user();
        $trashedSubscriptions = $user->subscriptions()->onlyTrashed()->paginate(10);

        return view('subscriptions.trash', compact('trashedSubscriptions'));
    }

    /**
     * Restore a soft-deleted subscription.
     */
    public function restore(int $id): RedirectResponse
    {
        $subscription = Auth::user()->subscriptions()->onlyTrashed()->findOrFail($id);
        $subscription->restore();

        return redirect()->route('subscriptions.trash')
            ->with('success', "Subscription \"{$subscription->name}\" has been restored!");
    }

    /**
     * Permanently delete a subscription.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $subscription = Auth::user()->subscriptions()->onlyTrashed()->findOrFail($id);
        $name = $subscription->name;
        $subscription->forceDelete();

        return redirect()->route('subscriptions.trash')
            ->with('success', "Subscription \"{$name}\" has been permanently deleted.");
    }

    /**
     * Mark as paid and advance renewal schedule by 1 cycle.
     */
    public function renew(Subscription $subscription): RedirectResponse
    {
        $this->authorizeSubscription($subscription);

        $currentDate = $subscription->next_billing_date ? Carbon::parse($subscription->next_billing_date) : Carbon::today();
        $baseDate = $currentDate->isPast() ? Carbon::today() : $currentDate;

        $nextDate = match ($subscription->billing_cycle) {
            'Weekly' => $baseDate->copy()->addWeek(),
            'Yearly' => $baseDate->copy()->addYear(),
            default => $baseDate->copy()->addMonth(),
        };

        $updates = [
            'next_billing_date' => $nextDate->toDateString(),
        ];

        // If it was a free trial, automatically activate it when marked as paid
        if ($subscription->status === 'Free Trial') {
            $updates['status'] = 'Active';
        }

        $subscription->update($updates);

        return back()->with('success', "Renewal recorded for {$subscription->name}! Next payment advanced to {$nextDate->format('M d, Y')}.");
    }

    /**
     * Export all subscriptions to a CSV file.
     */
    public function exportCsv()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()->orderBy('next_billing_date', 'asc')->get();

        $filename = 'subtrack_subscriptions_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($subscriptions, $user) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Service Name',
                'Category',
                'Cost',
                'Currency',
                'Billing Cycle',
                'Monthly Cost Equivalent',
                'Next Billing Date',
                'Status',
                'Notes',
            ]);

            foreach ($subscriptions as $sub) {
                fputcsv($file, [
                    $sub->id,
                    $sub->name,
                    $sub->category,
                    $sub->cost,
                    $user->currency,
                    $sub->billing_cycle,
                    $sub->monthly_cost,
                    $sub->next_billing_date ? $sub->next_billing_date->format('Y-m-d') : '',
                    $sub->status,
                    $sub->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ensure the current user owns the subscription.
     */
    protected function authorizeSubscription(Subscription $subscription): void
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
