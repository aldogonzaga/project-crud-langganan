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
    public function create(): View
    {
        $categories = ['Entertainment', 'Utilities', 'Health', 'Work', 'Education', 'Food', 'Shopping', 'Others'];
        $cycles = ['Monthly', 'Yearly', 'Weekly'];
        $statuses = ['Active', 'Free Trial', 'Cancelled'];

        return view('subscriptions.create', compact('categories', 'cycles', 'statuses'));
    }

    /**
     * Store a newly created subscription in storage.
     */
    public function store(Request $request): RedirectResponse
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

        return redirect()->route('subscriptions.index')
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
     * Ensure the current user owns the subscription.
     */
    protected function authorizeSubscription(Subscription $subscription): void
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
