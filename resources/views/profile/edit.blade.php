@extends('layouts.app')

@section('title', 'Profile & Settings')
@section('page_title', 'Account Settings & Preferences')
@section('page_subtitle', 'Manage your profile details, currency symbol, and security credentials.')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-12 col-lg-6">
        <!-- Personal Info & Preferences -->
        <div class="card-custom mb-4">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-person-gear me-2 text-primary" style="color: var(--pri) !important;"></i>
                Personal Profile & Currency
            </h5>
            <p class="text-muted small mb-4">Update your display name, contact email, and default currency symbol.</p>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-custom @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-custom @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="currency" class="form-label fw-semibold small">Preferred Currency Symbol <span class="text-danger">*</span></label>
                    <select class="form-select form-select-custom @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                        <option value="Rp" {{ old('currency', $user->currency ?? 'Rp') === 'Rp' ? 'selected' : '' }}>IDR (Rp) - Indonesian Rupiah</option>
                        <option value="$" {{ old('currency', $user->currency) === '$' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                        <option value="€" {{ old('currency', $user->currency) === '€' ? 'selected' : '' }}>EUR (€) - Euro</option>
                        <option value="£" {{ old('currency', $user->currency) === '£' ? 'selected' : '' }}>GBP (£) - British Pound</option>
                        <option value="¥" {{ old('currency', $user->currency) === '¥' ? 'selected' : '' }}>JPY (¥) - Japanese Yen</option>
                    </select>
                    <div class="form-text small text-muted">All charts, totals, and subscription prices will automatically format using this currency.</div>
                    @error('currency')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-theme">
                    <i class="bi bi-check-lg"></i> Save Preferences
                </button>
            </form>
        </div>

        <!-- Account Statistics Summary Card -->
        <div class="card-custom">
            <h6 class="fw-bold mb-3">Account Overview</h6>
            <div class="d-flex flex-column gap-2 small">
                <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--bd) !important;">
                    <span class="text-muted">Registered Account:</span>
                    <span class="fw-semibold">{{ $user->created_at->format('F d, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: var(--bd) !important;">
                    <span class="text-muted">Active Subscriptions:</span>
                    <span class="fw-semibold">{{ $user->subscriptions()->where('status', 'Active')->count() }} services</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Total Subscriptions Tracked:</span>
                    <span class="fw-semibold">{{ $user->subscriptions()->count() }} total records</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Password -->
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-shield-lock me-2 text-warning"></i>
                Security & Password
            </h5>
            <p class="text-muted small mb-4">Ensure your account is using a secure, long password.</p>

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold small">Current Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-custom @error('current_password') is-invalid @enderror" 
                           id="current_password" name="current_password" required placeholder="••••••••">
                    @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small">New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-custom @error('password') is-invalid @enderror" 
                           id="password" name="password" required placeholder="At least 6 characters">
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold small">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-custom" 
                           id="password_confirmation" name="password_confirmation" required placeholder="Re-type new password">
                </div>

                <button type="submit" class="btn btn-theme-secondary">
                    <i class="bi bi-key-fill"></i> Change Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

