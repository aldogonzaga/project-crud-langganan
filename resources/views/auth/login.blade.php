@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <div class="text-center mb-4">
        <h2 class="fw-bold fs-4 mb-1">Welcome back</h2>
        <p class="text-muted small mb-0">Sign in to track your subscriptions and upcoming bills.</p>
    </div>

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-3 small py-2 px-3 mb-3 border-0" style="background: var(--sec); color: var(--pri);">
            <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
            <button type="button" class="btn-close small p-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 px-3 mb-3 border-0" style="background: var(--dab); color: var(--da);">
            <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
            <button type="button" class="btn-close small p-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input type="email" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email', 'demo@example.com') }}" required autocomplete="email" autofocus placeholder="you@example.com">
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-semibold small mb-0">Password</label>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 rounded-start-4 ps-3" style="border-color: var(--bd);">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" class="form-control form-control-custom rounded-end-4 border-start-0 ps-2 @error('password') is-invalid @enderror" 
                       id="password" name="password" value="password123" required autocomplete="current-password" placeholder="••••••••">
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                <label class="form-check-label small text-muted" for="remember">
                    Remember me
                </label>
            </div>
        </div>

        <button type="submit" class="btn-theme mb-3">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Sign In</span>
        </button>
    </form>

    <div class="position-relative text-center my-3">
        <hr style="border-color: var(--bd);">
        <span class="position-absolute top-50 start-50 translate-middle px-3 small text-muted" style="background: var(--card);">or try demo</span>
    </div>

    <!-- 1-Click Demo Login Button -->
    <a href="{{ route('demo.login') }}" class="btn-theme-secondary mb-3">
        <i class="bi bi-lightning-charge-fill text-warning"></i>
        <span>1-Click Demo Login (Preloaded Data)</span>
    </a>

    <div class="text-center mt-3 pt-2 border-top" style="border-color: var(--bd) !important;">
        <span class="small text-muted">Don't have an account?</span>
        <a href="{{ route('register') }}" class="small fw-bold text-decoration-none ms-1" style="color: var(--pri);">
            Create an Account
        </a>
    </div>
@endsection
