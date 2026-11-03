<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sign In') - SubTrack | Pelacak Langganan</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --pri: #4f46e5;
            --pri-hover: #4338ca;
            --sec: #eef2ff;
            --bg: #f8fafc;
            --card: #ffffff;
            --tx: #0f172a;
            --mu: #64748b;
            --bd: #e2e8f0;
            --ok: #10b981;
            --okb: #ecfdf5;
            --da: #ef4444;
            --dab: #fef2f2;
            --sh: 0 12px 40px rgba(15, 23, 42, 0.08);
        }

        [data-bs-theme="dark"] {
            --pri: #6366f1;
            --pri-hover: #818cf8;
            --sec: #1e1b4b;
            --bg: #0b0f19;
            --card: #131b2e;
            --tx: #f8fafc;
            --mu: #94a3b8;
            --bd: #1e293b;
            --ok: #34d399;
            --okb: rgba(16, 185, 129, 0.15);
            --da: #f87171;
            --dab: rgba(239, 68, 68, 0.15);
            --sh: 0 12px 40px rgba(0, 0, 0, 0.45);
        }

        body {
            margin: 0;
            min-height: 100vh;
            background-color: var(--bg);
            color: var(--tx);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            opacity: 0.7;
            background: radial-gradient(circle at 15% 20%, rgba(79, 70, 229, 0.1), transparent 45%),
                        radial-gradient(circle at 85% 80%, rgba(6, 182, 212, 0.1), transparent 45%);
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background: var(--card);
            border: 1px solid var(--bd);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: var(--sh);
            backdrop-filter: blur(8px);
        }

        .brand-logo-center {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            color: var(--tx);
            font-weight: 800;
            font-size: 1.45rem;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }

        .brand-icon-large {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--pri), #06b6d4);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }

        .btn-theme {
            background: var(--pri);
            color: #ffffff;
            border: 0;
            border-radius: 999px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
            text-decoration: none;
        }

        .btn-theme:hover {
            background: var(--pri-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
        }

        .btn-theme-secondary {
            background: var(--sec);
            color: var(--pri);
            border: 1px solid var(--bd);
            border-radius: 999px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-theme-secondary:hover {
            background: var(--bd);
            color: var(--pri-hover);
        }

        .form-control-custom, .form-select-custom {
            background: var(--card);
            border: 1.5px solid var(--bd);
            border-radius: 14px;
            padding: 11px 16px;
            font-size: 0.92rem;
            color: var(--tx);
            transition: all 0.2s ease;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background: var(--card);
            border-color: var(--pri);
            color: var(--tx);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
            outline: none;
        }

        .theme-toggle-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }
    </style>
</head>
<body>
    <!-- Theme Toggle in Corner -->
    <div class="theme-toggle-fixed">
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 shadow-sm" onclick="toggleTheme()" id="theme-btn">
            <i class="bi bi-moon-stars" id="theme-icon"></i>
            <span id="theme-text" class="ms-1">Dark</span>
        </button>
    </div>

    <div class="auth-container">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="brand-logo-center">
            <div class="brand-icon-large">
                <i class="bi bi-wallet2"></i>
            </div>
            <span>SubTrack</span>
        </a>

        <!-- Auth Card -->
        <div class="auth-card">
            @yield('content')
        </div>

        <div class="text-center mt-4 text-muted small">
            &copy; {{ date('Y') }} SubTrack &bull; Pelacak Langganan & Uji Coba Gratis
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('subtrack-theme', theme);

            const icon = document.getElementById('theme-icon');
            const text = document.getElementById('theme-text');

            if (theme === 'dark') {
                if (icon) icon.className = 'bi bi-sun';
                if (text) text.textContent = 'Light';
            } else {
                if (icon) icon.className = 'bi bi-moon-stars';
                if (text) text.textContent = 'Dark';
            }
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        }

        const savedTheme = localStorage.getItem('subtrack-theme') || 'light';
        applyTheme(savedTheme);
    </script>
</body>
</html>

