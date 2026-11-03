@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <div class="text-center mb-4">
        <h2 class="fw-bold fs-4 mb-1">Create your account</h2>
        <p class="text-muted small mb-0">Start tracking and saving on recurring subscriptions.</p>
    </div>

    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold small">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
            </div>
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input type="email" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com">
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="currency" class="form-label fw-semibold small">Preferred Currency</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                    <i class="bi bi-currency-exchange text-muted"></i>
                </span>
                <select class="form-select form-select-custom rounded-end-4 border-start-0 ps-2 @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                    <option value="$" {{ old('currency', '$') === '$' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                    <option value="Rp" {{ old('currency') === 'Rp' ? 'selected' : '' }}>IDR (Rp) - Indonesian Rupiah</option>
                    <option value="€" {{ old('currency') === '€' ? 'selected' : '' }}>EUR (€) - Euro</option>
                    <option value="£" {{ old('currency') === '£' ? 'selected' : '' }}>GBP (£) - British Pound</option>
                    <option value="¥" {{ old('currency') === '¥' ? 'selected' : '' }}>JPY (¥) - Japanese Yen</option>
                </select>
            </div>
            @error('currency')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold small">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('password') is-invalid @enderror" 
                       id="password" name="password" required autocomplete="new-password" placeholder="At least 6 characters">
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold small">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                    <i class="bi bi-shield-check text-muted"></i>
                </span>
                <input type="password" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2" 
                       id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Re-type password">
            </div>
        </div>

        <button type="submit" class="btn-theme mb-3">
            <i class="bi bi-person-check-fill"></i>
            <span>Create Account</span>
        </button>
    </form>

    <div class="text-center mt-3 pt-2 border-top" style="border-color: var(--bd) !important;">
        <span class="small text-muted">Already have an account?</span>
        <a href="{{ route('login') }}" class="small fw-bold text-decoration-none ms-1" style="color: var(--pri);">
            Sign In
        </a>
    </div>
@endsection

