<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TrialController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/demo-login', [AuthController::class, 'quickDemoLogin'])->name('demo.login');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Trashed Subscriptions (defined before resource routes with {subscription})
    Route::get('/subscriptions/trash', [SubscriptionController::class, 'trash'])->name('subscriptions.trash');
    Route::post('/subscriptions/{id}/restore', [SubscriptionController::class, 'restore'])->name('subscriptions.restore');
    Route::delete('/subscriptions/{id}/force', [SubscriptionController::class, 'forceDelete'])->name('subscriptions.forceDelete');

    // Subscriptions CRUD
    Route::resource('subscriptions', SubscriptionController::class);
    Route::patch('/subscriptions/{subscription}/status', [SubscriptionController::class, 'quickStatus'])->name('subscriptions.quickStatus');

    // Free Trials Dedicated Management
    Route::get('/trials', [TrialController::class, 'index'])->name('trials.index');
    Route::patch('/trials/{subscription}/cancel', [TrialController::class, 'cancel'])->name('trials.cancel');
    Route::patch('/trials/{subscription}/activate', [TrialController::class, 'activate'])->name('trials.activate');

    // Visual Analytics & Charts
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Profile & Preferences
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
