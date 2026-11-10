<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show profile & settings page.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'currency' => ['required', 'string', 'max:10'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile and preferences updated successfully!');
    }

    /**
     * Fast update monthly budget limit.
     */
    public function updateBudget(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'monthly_budget' => ['required', 'numeric', 'min:0'],
        ]);

        Auth::user()->update($validated);

        return back()->with('success', 'Monthly budget limit updated!');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}
